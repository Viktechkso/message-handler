Message Handler
===============

Message Handler is simply data collector, transformer and provider.

You can create reprogrammable flows of data that can contain data collecting,
transformation with some DataMaps and pushing to an external targets.

All flows can be scheduled to run at a specific time, like Linux CRONs.

The products of all flows are objects called Messages.
Message are divided to steps, and contains information about used data provider,
transformations done on the managed data, and the status of data pushing to external targets.


Installation
------------

* Clone the base application repository
* Create main database
* Run composer install
* Clone plugins repositories in to `plugins/` directory
* Create app/config/plugins.yml configuration from the `plugins.yml.dist` file. To dump all available configuration nodes, run:

    ```
    php app/console config:dump VRAppBundle
    ```

* Copy (and adjust) example plugins configuration from `plugins/PLUGIN_DIRECTORY/plugins.yml.example` to `app/config/plugins.yml`
* Run some tests to check if the instance is installed and configured correctly:

    ```
    php app/console sugarcrm:test-connections
    ```


Standard Command Line Tasks
---------------------------

* Main entry point for CRON schedules. Must be set in system's CRON to one minute interval.

    ```
    cron:run
    ```

* Main entry point for parsing forced Messages. Must be set in system's CRON to one minute interval.

    ```
    cron:run-forced
    ```

* Deletes messages older than given number of months.

    ```
    messages:archive
    ```

Useful Command Line Tasks
-------------------------

* Shows current allocation of Schedules in multithreading environment.

    ```
    cron:show-allocation
    ```

* Runs scheduled process for test. Time-matching conditions are skipped.
  Parameters used in process are taken from Schedule object, so they can be set in GUI.
  To see the list of available Schedules, just run this task without parameters.

    ```
    cron:test
    ```

* Tests internal processes, like Collectors or Workers.
  Parameters used in process are taken from the command line (you will be asked for them or you can provide them as an argument).
  To see the list of available processes, just run this task without parameters.

    ```
    process:test
    ```

* Downloads DataMaps from the given instance.
  Useful for transferring DataMaps for example from Production to Development machine for debugging.

    ```
    datamaps:update http://url-of-the-source-instance/
    ```

* Tests configured connections to SugarCRM instances.

    ```
    sugarcrm:test-connections
    ```

* Clears all locks (like lock files in app/locks)

    ```
    cron:clear-locks
    ```

* Application configuration dump

    ```
    config:dump VRAppBundle
    ```

Installing Plugins
------------------

To extend Message Handler functionality, you can install plugins.
Usally plugins contains Collectors and Workers used to create data flows and transformations.

* Clone GIT repository to the `plugins/` directory

    ````
    cd plugins/
    git clone git@repositories.dev:repository-name.git FullNameOfTheBundle
    ````

* Create plugin's configuration in `app/config/plugins.yml`

* Enable wanted plugin's run modes in `plugins.enabled_run_modes` node in `app/config/plugins.yml`

Each plugin has `plugins.yml.example` file, that contains example configuration needed to run the plugin
correctly by the Message Handler.


Creating Plugins
----------------

Plugins are internally normal Symfony 2 Bundles with functionality focused on extending data flows in Message Handler.

* Generate normal Symfony 2 Bundle with `full files structure` from the Command Line

    ```
    generate:bundle
    ```

* Move the new Bundle's directory from `src/` to the `plugins/` directory


* Edit `TheNewBundle.php` - the main Bundle class, and change `extends Bundle` to `extends PluginBundle` from the Message Handler namespace

    ```
    namespace ExampleCollectorPluginBundle;
    
    use VR\AppBundle\Plugin\PluginBundle;
    
    class ExampleCollectorPluginBundle extends PluginBundle
    {
    }
    ```

* Create `initPlugin()` method in main Bundle's class

    ```
    namespace ExampleCollectorPluginBundle;
        
    use VR\AppBundle\Plugin\PluginBundle;
    
    class ExampleCollectorPluginBundle extends PluginBundle
    {
        public function initPlugin()
        {
            $this->setPluginType('collector');
            $this->setPluginShortcut('example');
            $this->setPluginName('Example Collector');
    
            # Run modes
            $this->addRunMode('main', 'Example Main Collector');
            $this->addRunMode('second', 'Example Second Collector');
    
            # Run modes parameters (used eg. in Schedules)
            $this->addParameter('main', 'example-parameter-a', 'integer', false, 'Example description');
            $this->addParameter('main', 'example-parameter-b', 'integer', false, 'Example description');
            $this->addParameter('second', 'example-parameter-c', 'date', false, 'Example description');
            
            # Run modes message types (used eg. in search box)
            $this->addMessageType('main', 'example_type', 'Example Type');
        }
    }
    ```

* Create bootstrap service in `plugin_root/Resources/config/services.yml`

    ```
    plugin.PLUGIN_TYPE.PLUGIN_SHORTCUT.bootstrap:
        class: VR\ExampleCollectorPluginBundle\Service\Bootstrap
        arguments: [@service_container]
    ```

* Create bootstrap class in `plugin_root/Service/Bootstrap.php`

    ```
    namespace VR\ExamplePluginBundle\Service;
    
    use Symfony\Component\DependencyInjection\Container;
    
    class Bootstrap
    {
        /** @var Container */
        protected $container;
    
        public function __construct($container)
        {
            $this->container = $container;
        }
    
        public function run($runMode, $parameters)
        {
            switch ($runMode) {
                case 'main':
                    $this->container->get('vr.PLUGIN_TYPE.PLUGIN_SHORTCUT.custom_service_name_for_main_run_mode')->run($parameters);
                    break;
                case 'second':
                    $this->container->get('vr.PLUGIN_TYPE.PLUGIN_SHORTCUT.custom_service_name_for_second_run_mode')->run($parameters);
                    break;
                default:
                    throw new \Exception('Unknown Run Mode "' . $runMode . '"');
            }
        }
    }
    ```

* Create your own plugin's functionality, like in normal Symfony 2 Bundle