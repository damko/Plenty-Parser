<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
* @name Plenty Parser
* @copyright 2011
* @author Dwayne Charrington
* @license http://philsturgeon.co.uk/code/dbad-license
* @version 1.0
*/

class Pp extends CI_Driver_Library {
    
    /**
    * Codeigniter instance
    * 
    * @var mixed
    */
    protected $ci;
    
    /**
    * The driver to use for rendering
    * 
    * @var mixed
    */
    protected $_current_driver;
    
    /**
    * Current theme in use
    * 
    * @var mixed
    */
    protected $_current_theme;
    
    /**
    * Get default theme (if any)
    * 
    * @var mixed
    */
    protected $_default_theme;
    
    /**
    * Theme locations
    * 
    * @var mixed
    */
    protected $_theme_locations = array();
    
    /**
    * Are theming capabilities enabled or disabled?
    * 
    * @var mixed
    */
    protected $_theme_enabled;
    
    /**
    * Valid drivers for rendering views
    * 
    * @var mixed
    */
    protected $valid_drivers = array(
        'pp_smarty',
        'pp_twig',
    );
    
    /**
    * Constructor
    * 
    */
    public function __construct()
    {
        $this->ci = get_instance();
        $this->ci->config->load('plentyparser');
        
        // Get our default driver
        $this->_current_driver = config_item('parser.driver');
        
        // Get default theme (if one set)
        $this->_default_theme = config_item('parser.theme.default');
        
        // Get whether or not themes are enabled or disabled
        $this->_theme_enabled = config_item('parser.theme.enabled');
        
        // Call the init function
        $this->_init();
    }
    
    /**
    * Init
    * Populates variables and other things
    * @returns void
    */
    private function _init()
    {
        // Is theming capability enabled
        if ($this->_theme_enabled)
        {
            // Get all paths defined in the config file and add them to our array
            foreach (config_item('parser.theme.locations') AS $path)
            {
                // If path isn't already in our array of paths
                if ( !array_key_exists($path, $this->_theme_locations) )
                {
                    $this->_theme_locations[$path] = '';
                }
            }
            
            // If no theme is set and we have a default, use that
            if ( empty($this->_current_theme) AND !empty($this->_default_theme) )
            {
                $this->_current_theme = $this->_default_theme;
            }
            
        }
    }
    
    /**
    * Call
    * able to call native Smarty methods
    * @returns void
    */
    public function __call($method, $params=array())
    {
		
	if(!method_exists($this, $method))
    {
		call_user_func_array(array($this->{$this->_current_driver}, $method), $params);
			
		}
    }
    
    /**
    * Set Driver
    * Sets which driver to use for view rendering
    * 
    * @param mixed $driver
    * @returns void
    */
    public function set_driver($driver)
    {
        $driver = trim($driver);
        
        if ($driver == $this->_current_driver)
        {
            return true;
        }
        else
        {
            $this->_current_driver = $driver;   
        }
    }
    
    /**
    * Set Theme
    * Set the theme name we're using
    * 
    * @param mixed $theme
    * @returns void
    */
    public function set_theme($theme)
    {
        $theme = trim($theme);
        
        if ($theme == $this->_current_theme)
        {
            return true;
        }
        else
        {
            $this->_current_theme = $theme;   
        }
    }
    
    /**
    * Add Theme Location
    * Add a new theme location
    * 
    * @param mixed $location
    * @returns void
    */
    public function add_theme_location($location)
    {
        // If path isn't already in our array of paths
        if ( !array_key_exists($location, $this->_theme_locations) )
        {
            $this->_theme_locations[$location] = '';
        }
        
        return true;
    }
    
    /**
    * Assign Var
    * Set a variable name and value for a template
    * 
    * @param mixed $name
    * @param mixed $value
    * @returns void
    */
    public function assign_var($name, $value)
    {
        return $this->{$this->_current_driver}->assign_var($name, $value);
    }
    
    /**
    * Parse
    * Parse will return the contents instead of displaying
    * 
    * @param mixed $template
    * @param mixed $data
	* @param string $hmvc_module  //looks into $hmvc_module to find the views folder
    * @returns void
    */
    public function parse($template, $data = array(), $return = false, $driver = '', $hmvc_module = null)
    {
    	//loads phpgettext config
    	if(function_exists('setupPhpGettext')) {
    		setupPhpGettext();
    	}
    	
        // Are we setting a particular driver to render with?
        if ($driver !== '')
        {
            $this->_current_driver = trim($driver);
        }       
        
        // Add in the extension using the default if not supplied
        if (!stripos($template, "."))
        {
            // If we have a parser template extension defined
            if (config_item('parser.'.$this->_current_driver.'.extension'))
            {
                $template = $template.config_item('parser.'.$this->_current_driver.'.extension');   
            }
            else
            {
                show_error('No extension has been defined for the driver "'.$this->_current_driver.'". Please define one in the plentyparser.php config file.');
            }
        }  

        $template_file = config_item('parser.smarty.location') . '/' . $template;
        
        $file_not_found = false;

		//DAM if themes or hmvc are enabled it looks for alternatives paths        
        if(is_file($template_file) && is_null($hmvc_module)) {
        	
        	//in the hmvc the default smarty directory keeps changing, so it's necessary to set it to the default
        	$this->{$this->_current_driver}->set_template_dir(config_item('parser.smarty.location'));
        	
        } else {
        	$file_not_found = true;
        	
        	if(!empty($hmvc_module))
        	{
        		list($path, $view) = Modules::find($template, $hmvc_module, 'views/');
        		if($path) {
        			//$this->ci->smarty_view_path = $path;
        			$this->{$this->_current_driver}->set_template_dir($path);
        			$file_not_found = false;
        		}
        	}
        	
        	if($file_not_found && is_array($this->_theme_locations) and !empty($this->_current_theme)){
        		
        		foreach (array_keys($this->_theme_locations) as $theme_location){
        			
        			$tryfile = $theme_location . '/' . $this->_current_theme . '/' .$template;
        			
        			if(is_file($tryfile)) {
        				$this->{$this->_current_driver}->set_template_dir($theme_location . '/' . $this->_current_theme);
        				$file_not_found = false;
        				break;
        			}
        		}
        		
        	}
        } 
        
        if($file_not_found){
        	echo $template . ' is not a file';
        	return false;
        }
        
        // Call the driver parse function
        return $this->{$this->_current_driver}->parse($template, $data, $return);
    }
    
    /**
    * Parse String
    * Parse a string and return it as a string or display it
    * 
    * @param mixed $string
    * @param mixed $data
    * @param mixed $return
    * @returns void
    */
    public function parse_string($string, $data = array(), $return = false)
    {
        return $this->{$this->_current_driver}->parse_string($string, $data, $return);
    } 
    
}
