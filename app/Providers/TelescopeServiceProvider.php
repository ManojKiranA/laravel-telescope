<?php

namespace App\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Fluent;
use Laravel\Telescope\EntryType;
use Laravel\Telescope\Telescope;
use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\TelescopeApplicationServiceProvider;
use Browser;
use PHPSQLParser\PHPSQLParser;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Telescope::night();

        Telescope::tag(function (IncomingEntry $entry) {

            $tagsArray = [];

            $tagsArray[EntryType::REQUEST] = function(IncomingEntry $entry){

                $browserObject = new Browser();
                $fluent = new Fluent;

                $fluent->HttpStatusCode = $entry->content['response_status'];
                $fluent->HttpMethod = $entry->content['method'];
                $fluent->IpAddress = request()->header('x-forwarded-for',request()->ip());
                $fluent->ControllerAction = $entry->content['controller_action'];
                $fluent->HostName = $entry->content['hostname'];

                $fluent->BrowserName = $browserObject->getBrowser();
                $fluent->Platform = $browserObject->getPlatform();

                if(Auth::check()){
                    $fluent->AuthEmail = Auth::user()->email;
                }

                return $this->convertCollectionToTags($fluent);
            };

            $tagsArray[EntryType::COMMAND] = function(IncomingEntry $entry){
                
                $fluent = new Fluent;

                $fluent->CommandName = $entry->content['command'];
                $fluent->ExitCode = $entry->content['exit_code'];

                return $this->convertCollectionToTags($fluent);
            };

            $tagsArray[EntryType::JOB] = function(IncomingEntry $entry){                
                $fluent = new Fluent;

                if($queueName = $entry->content['queue']){
                    $fluent->QueueName = $queueName;
                }

                if($connectionName = $entry->content['connection']){
                    $fluent->ConnectionName = $connectionName;
                }

                $fluent->HostName = $entry->content['hostname'];
                $fluent->JobClass = $entry->content['name'];

                return $this->convertCollectionToTags($fluent);
            };

            $tagsArray[EntryType::LOG] = function(IncomingEntry $entry){
                
                $fluent = new Fluent;
                $fluent->Level = ucfirst($entry->content['level']);

                return $this->convertCollectionToTags($fluent);
            };

            $tagsArray[EntryType::VIEW] = function(IncomingEntry $entry){
                
                $fluent = new Fluent;

                $fluent->FileName = Str::of($entry->content['path'])->replace(['/', '\\'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR])->__toString();
                $fluent->FileRealPath = Str::of($entry->content['path'])->replace(['/', '\\'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR])->prepend(base_path())->__toString();

                return $this->convertCollectionToTags($fluent);
            };

            $tagsArray[EntryType::QUERY] = function(IncomingEntry $entry){


                $fluent = new Fluent;

                $sqlParser = (new PHPSQLParser($entry->content['sql']))->parsed;
                $tableNames = Arr::get($sqlParser,'FROM');
                $firstTableName = data_get($tableNames,'0.table');

                $fluent->Connection = $entry->content['connection'];
                if($firstTableName){
                    $fluent->TableName = Str::of($firstTableName)->trim('`');
                }

                return $this->convertCollectionToTags($fluent);
            };

            $tagsArray[EntryType::MODEL] = function(IncomingEntry $entry){

                $modelName = Str::before($entry->content['model'], ':');
                $modelAction = ucfirst($entry->content['action']);

                $fluent = new Fluent;

                $fluent->ModelAction = $modelAction;
                $fluent->ModelName = $modelName;
                $fluent->ModelNameAndAction = Str::of($modelName)->append('||-||')->append($modelAction)->__toString();

                return $this->convertCollectionToTags($fluent);
            };

            $tagsArray[EntryType::EVENT] = function(IncomingEntry $entry){

                $fluent = new Fluent;

                $fluent->EventName = $entry->content['name'];
                $fluent->HostName = $entry->content['hostname'];
                $fluent->Broadcasting = $entry->content['broadcast'] ? 'YES':'NO';

                return $this->convertCollectionToTags($fluent);
            };

            $tagsArray[EntryType::MAIL] = function(IncomingEntry $entry){

                $entry->tags([
                    'MailableClass:'.$entry->content['mailable'],
                    Str::of('Queued:')
                    ->append($entry->content['queued'] ? 'YES':'NO')
                    ->__toString(),
                ]);

                return $entry->tags;
            };

            $tagsArray[EntryType::NOTIFICATION] = function(IncomingEntry $entry){

                $fluent = new Fluent;

                $fluent->NotificationClass = $entry->content['notification'];
                $fluent->ChannelName = $entry->content['channel'];
                $fluent->Queued = $entry->content['queued'] ? 'YES':'NO';
                $fluent->HostName = $entry->content['hostname'];

                return $entry->tags($this->convertCollectionToTags($fluent))->tags;
            };

            $tagsArray[EntryType::GATE] = function(IncomingEntry $entry){
                $fluent = new Fluent;

                $fluent->Ability = $entry->content['ability'];
                $fluent->HostName = $entry->content['hostname'];
                
                return $entry->tags($this->convertCollectionToTags($fluent))->tags;
            };

            $tagsArray[EntryType::CACHE] = function(IncomingEntry $entry){

                $fluent = new Fluent;

                $fluent->CacheAction = ucfirst($entry->content['type']);
                $fluent->CacheKey = $entry->content['key'];
                $fluent->CacheActionAndKey = Str::of($entry->content['type'])->ucfirst()->append('||-||')->append($entry->content['key'])->__toString();
                $fluent->CacheKey = $entry->content['key'];
                $fluent->HostName = $entry->content['hostname'];
                
                return $entry->tags($this->convertCollectionToTags($fluent))->tags;
            };

            $dummyTags = [];

            $tagCheck = $tagsArray;
            // $tagCheck = $dummyTags;

            if($callback = Arr::get($tagCheck,$entry->type,null)){
                return call_user_func($callback,$entry);
            }

            return [];
        });

        $this->hideSensitiveRequestDetails();

        Telescope::filter(function (IncomingEntry $entry) {
            if ($this->app->environment('local')) {
                return true;
            }

            return $entry->isReportableException() ||
                   $entry->isFailedRequest() ||
                   $entry->isFailedJob() ||
                   $entry->isScheduledTask() ||
                   $entry->hasMonitoredTag();
        });
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     *
     * @return void
     */
    protected function hideSensitiveRequestDetails()
    {
        if ($this->app->environment('local')) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewTelescope', function ($user) {
            return in_array($user->email, [
                //
            ]);
        });
    }

    /**
     * Converts Collection to tags
     *
     * @return array
     **/
    public function convertCollectionToTags(Fluent $fluent)
    {
        return  collect($fluent)
                        ->map(function($value,$key){
                            return $key.':'.$value;
                        })
                        ->values()
                        ->toArray();
    }
}
