<?php

namespace App\View\Components;

use Illuminate\View\Component;

class WelcomePageLinks extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the List of Routes with Display name
     *
     * @return array
     **/
    public function routesWithNameAndLinks(): array
    {
        $routes = [
            [
                'routename' => 'request',
                'displayName' => 'Request',
            ],
            [
                'routename' => 'commands',
                'displayName' => 'Commands',
            ],
            [
                'routename' => 'jobs',
                'displayName' => 'Jobs',
            ],
            [
                'routename' => 'exceptions',
                'displayName' => 'Exception',
            ],

            [
                'routename' => 'logs',
                'displayName' => 'Logs',
            ],

            [
                'routename' => 'views',
                'displayName' => 'View',
            ],

            [
                'routename' => 'queries',
                'displayName' => 'Queries',
            ],

            [
                'routename' => 'models',
                'displayName' => 'Models',
            ],

            [
                'routename' => 'events',
                'displayName' => 'Events',
            ], 
            
            [
                'routename' => 'emails',
                'displayName' => 'Emails',
            ],

            [
                'routename' => 'notifications',
                'displayName' => 'Notifications',
            ],
            [
                'routename' => 'gates',
                'displayName' => 'Gates',
            ],

            [
                'routename' => 'cache',
                'displayName' => 'Cache',
            ],
                        
        ];

        return $routes;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.welcome-page-links');
    }
}
