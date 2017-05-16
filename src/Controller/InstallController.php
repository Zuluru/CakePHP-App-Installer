<?php
namespace Installer\Controller;

use Installer\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Filesystem\File;

/**
* Install Controller
*
* @property \Installer\Model\Table\InstallTable $Install
*
* @method \Installer\Model\Entity\Install[] paginate($object = null, array $settings = [])
*/
class InstallController extends AppController
{
    /**
    * Default configuration
    *
    * @access	public
    * @return	void
    */
    public $DEFAULT_CONFIG = array(
        'className'  => 'Cake\Database\Connection',
        'driver'     => 'Cake\Database\Driver\Mysql',
        'persistent' => false,
        'host'       => 'localhost',
        'username'   => 'root',
        'password'   => '',
        'database'   => 'cakephp',
        'prefix'     => '',
        'encoding'   => 'UTF8',
    );

    /**
    * beforeFilter
    *
    * @access	public
    * @return	void
    */
    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
    }

    /**
    * Check wheter the installation file already exists
    *
    * @access	public
    * @return	void
    */
    protected function _check(){
        if(Configure::read('Database.installed') == true) {
            $this->Flash->success(__("Website already configured"));
            $this->redirect('/');
        }
    }


    /**
    * STEP 1 - CONFIGURATION TEST
    *
    * @access	public
    * @return	void
    */
    public function index() {
        $this->_check();
        $d['title_for_layout'] = __("Configuration Check");
        $this->set($d);
    }

    /**
    * STEP 2 - DATABASE CONNECTION TEST
    *
    * @access	public
    * @return	void
    */
    public function connection() {
        $this->_check();
        $d['title_for_layout'] = __("Database Connection Setup");
        if (!file_exists(CONFIG.'app.default.php')) {
            rename(CONFIG.'app.default.php', CONFIG.'app.php');
        }

        if($this->request->is('post')) {

            // loads the default configuration
            $config = $this->DEFAULT_CONFIG;

            // loads form data
            $data = $this->request->getData();

            // check if import_database is checked
            $import_database = $this->request->getData('import_database');

            // replaces default config by form data
            foreach($data as $k => $v) {
                if(isset($data[$k])) {
                    $config[$k] = $v;
                }
            }

            try {
                /**
                * Try to connect the database with the new configuration
                */
                ConnectionManager::config('my_default', $config);
                $db = ConnectionManager::get('my_default');
                if(!$db->connect()) {
                    $this->Flash->error(__("Cannot connect to the database"));
                } else {
                    /**
                    * We will create the true database_config.php file with our configuration
                    */
                    copy(PLUGIN_CONFIG.'database.php.install', CONFIG.'database_config.php');
                    $file = new File(CONFIG. 'database_config.php');
                    $content = $file->read();
                    foreach($config as $k => $v) {
                        $content = str_replace('{default_' .$k.  '}', $v, $content);
                    }

                    if($file->write($content)) {

                        $this->Flash->success(__("Connected to the database"));

                        // import database if import_database is checked
                        if ($import_database) {
                            $this->redirect(['action' => 'data']);
                        } else {
                            $this->redirect(['action' => 'finish']);
                        }

                    } else {
                        $this->Flash->error(__("database_config.php file cannot be modified"));
                    }
                }
            } catch(Exception $e) {
                $this->Flash->error(__("Cannot connect to the database"));
            }
        } // post
        $this->set($d);
    } //function

    /**
    * STEP 3 - DATABASE CONSTRUCTION
    *
    * @access	public
    * @return	void
    */
    public function data() {
        $this->_check();
        $d['title_for_layout'] = __("Database Construction");


        $db = ConnectionManager::get('default');

        // connection to the database
        if(!$db->connect()) {
            $database_connect = false;
        } else {
            $database_connect = true;
        }
        $this->set(compact('database_connect'));

        if($this->request->is('post')) {

            $db = ConnectionManager::get('default');

            // connection to the database
            if(!$db->connect()) {
                $this->Flash->error(__("Cannot connect to the database"));
            } else {
                $sql_file = new File(CONFIG.'schema'.DS.'my_schema.sql');
                if (!$sql_file->exists()) {
                    $this->Flash->error(__('Schema file does not exists. Make sure my_schema.sql exists in /config/schema/my_schema.sql'));
                } else {
                    if (!$sql_file->size() > 0) {
                        $this->Flash->error(__('It seems schema file is empty. Please check if schema exits at /config/schema/my_schema.sql'));
                    } else {
                        $sql_content = $sql_file->read();
                        // fetches all information of the tables of the Schema.php file (app/Config/Schema/Schema.php)
                        if ($db->execute($sql_content)) {
                            $this->Flash->success(__('Database imported'));
                        } else {
                            $this->Flash->error(__('Database Import Failed'));
                        }
                        $this->redirect(array('action' => 'finish'));
                    }
                }
            }
        }

        $this->set($d);
    } // function


    /**
    * STEP 4 - INSTALLATION COMPLETE
    *
    * @access	public
    * @return	void
    */
    public function finish() {
        $this->_check();
        $d['title_for_layout'] = __("Installation Complete");

        if(!$this->_changeConfiguration()){
         	$this->Flash->error(__("Cannot modify Database.installed variable in /plugins/Installer/config/bootstrap.php"));
        }

        $this->set($d);
    }

    /**
    * change Database.Installed to true
    */
    protected function _changeConfiguration() {
        $path = PLUGIN_CONFIG.'bootstrap.php';

        $file = new File($path);
        $contents = $file->read();
        $content_new = str_replace('false', 'true', $contents);
        if($file->write($content_new)) {
            return true;
        } else {
            return false;
        }
    }
}
