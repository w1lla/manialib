<?php
/**
 * MVC framwork magic happens here!
 * 
 * @author Maxime Raoust
 * @copyright 2009-2010 NADEO 
 * @package ManiaLib_MVC
 */

// FIXME actionName / controlerName are not robust because of the URL<->camelCase conversion. It should be done by Route?

/**
 * @ignore
 */
require_once(APP_MVC_FRAMEWORK_EXCEPTIONS_PATH.'MVCException.class.php'); 

/**
 * Action controller
 * 
 * This is the base class for all controllers. Extend ActionController to create
 * a new controller for your application.
 * 
 * Naming conventions: URLs should always be lowercase.
 * Controller names in the request will be mapped to CamelCase class names.
 * eg. /some_request/ will be mapped to SomeRequestController
 * You can change the default separator ("_") in the config using the APP_MVC_CONTROLLER_SEPARATOR constant.
 * <ul>
 * <li>class MySuperStuffController</li>
 * <li>public function MySuperStuffController::mysuperaction()</li>
 * </ul>
 * 
 * Example:
 * <code>
 * class HomeController extends ActionController
 * {
 *    function __construct()
 *    {
 *        parent::__construct();
 *        $this->addFilter(new RegisterRequestParametersFilter());
 *    }
 *    
 *    function index() {} // mapped by /home/index/
 *    
 *    function anotherAction() {} // mapped by /home/another_action/
 * }
 * </code>
 * 
 * @package ManiaLib_MVC
 * @todo Think about "plugins" eg. you want to do a shoutbox plugin, how everything works?
 */
class ActionController
{
	/**
	 * Overrride this to define the controller's default action name
	 * @var string
	 */
	protected $defaultAction = URL_PARAM_DEFAULT_ACTION;
	/**
	 * Current controller name 
	 */	
	protected $controllerName;
	/**
	 * @var array[Filterable]
	 */
	protected $filters = array();
	/**
	 * @var array[ReflectionMethod]
	 */
	protected $reflectionMethods = array();
	/**
	 * @var RequestEngineMVC
	 */
	protected $request;
	/**
	 * @var SessionEngine
	 */
	protected $session;
	/**
	 * @var ResponseEngine
	 */
	protected $response;
	
	final public static function dispatch()
	{
		$request = RequestEngineMVC::getInstance();
		self::getController($request->getController())->launch();
		ResponseEngine::getInstance()->render();
	}
	
	/**
	 * @return ActionController
	 */
	final static public function getController($controllerName)
	{
		$controllerClass = self::separatorToCamelCase($controllerName).'Controller';
		$controllerFilename = APP_MVC_CONTROLLERS_PATH.$controllerClass.'.class.php';

		if (!file_exists($controllerFilename))
		{
			throw new ControllerNotFoundException($controllerName);
		}

		require_once($controllerFilename);
		return new $controllerClass($controllerName);
	}
	
	final static protected function separatorToCamelCase($string)
	{
		return implode('', array_map('ucfirst', explode(APP_MVC_CONTROLLER_NAME_SEPARATOR, $string)));
	}

	/**
	 * If you want to do stuff at instanciation, override self::onConstruct()
	 */
	function __construct($controllerName)
	{
		$this->controllerName = $controllerName;
		$this->request = RequestEngineMVC::getInstance();
		$this->response = ResponseEngine::getInstance();
		$this->session = SessionEngine::getInstance();
		$this->onConstruct();
	}
	
	/**
	 * Stuff to be executed when the controller is instanciated; override this in your controllers 
	 */
	function onConstruct(){}

	final protected function addFilter(Filterable $filter)
	{
		$this->filters[] = $filter;
	}

	/**
	 * @return array[Filterable]
	 */
	final public function getFilters()
	{
		return $this->filters;
	}

	final protected function chainAction($controllerName=null, $actionName)
	{
		if($controllerName==null || $controllerName.'Controller' == get_class($this))
		{
			$this->checkActionExists($actionName);
			$this->executeAction($actionName);
		}
		else
		{
			$this->executeActionCrossController($controllerName,$actionName);
		}
	}

	final protected function chainActionAndView($controllerName=null, $actionName, $resetViews = true)
	{
		if($resetViews)
		{
			$this->response->resetViews();
		}
		if($controllerName==null || $controllerName == $this->controllerName)
		{
			$this->checkActionExists($actionName);
			$this->response->registerView($this->controllerName, $actionName);
			$this->executeAction($actionName);
		}
		else
		{
			$this->response->registerView($controllerName, $actionName);
			$this->executeActionCrossController($controllerName, $actionName);
		}
	}

	final public function checkActionExists($actionName)
	{
		$methodName = lcfirst(self::separatorToCamelCase($actionName));
		if(!array_key_exists($methodName, $this->reflectionMethods))
		{
			try
			{
				$this->reflectionMethods[$methodName] = new ReflectionMethod(get_class($this),$methodName);
			}
			catch(Exception $e)
			{
				throw new ActionNotFoundException($actionName);
			}
		}
		if(!$this->reflectionMethods[$methodName]->isPublic())
		{
			throw new ActionNotFoundException($actionName.' (Method "'.$methodName.'()" must be public)');
		}
		if($this->reflectionMethods[$methodName]->isFinal())
		{
			throw new Exception($actionName.' (Method "'.$methodName.'()" must not be final)');
		}
	}

	final protected function executeActionCrossController($controllerName, $actionName)
	{
		$controller = self::getController($controllerName);
		$controller->checkActionExists($actionName);
		$controllerFilters = $controller->getFilters();
		foreach($controllerFilters as $controllerFilter)
		{
			if(!in_array($controllerFilter,$this->filters))
			{
				$controllerFilter->preFilter();
			}
		}
		$controller->executeAction($actionName);
		foreach($controllerFilters as $controllerFilter)
		{
			if(!in_array($controllerFilter,$this->filters))
			{
				$controllerFilter->postFilter();
			}
		}
	}

	final public function executeAction($actionName)
	{
		$methodName = lcfirst(self::separatorToCamelCase($actionName));
		if(!array_key_exists($methodName, $this->reflectionMethods))
		{
			try
			{
				$this->reflectionMethods[$methodName] =
				new ReflectionMethod(get_class($this),$methodName);
			}
			catch(Exception $e)
			{
				throw new ActionNotFoundException($actionName);
			}
		}

		$callParameters = array();
		$requiredParameters = $this->reflectionMethods[$methodName]->getParameters();
		foreach($requiredParameters as $parameter)
		{
			if($parameter->isDefaultValueAvailable())
			{
				$callParameters[] = $this->request->get($parameter->getName(), $parameter->getDefaultValue());
			}
			else
			{
				$callParameters[] = $this->request->getStrict($parameter->getName());
			}
		}

		call_user_func_array(array($this, $methodName), $callParameters);
	}

	final protected function launch()
	{
		$actionName = $this->request->getAction($this->defaultAction);
		if(!$actionName) $actionName = $this->defaultAction;

		$this->checkActionExists($actionName);

		$this->response->registerView($this->controllerName, $actionName);

		foreach($this->filters as $filter)
		{
			$filter->preFilter();
		}

		$this->executeAction($actionName);

		foreach(array_reverse($this->filters) as $filter)
		{
			$filter->postFilter();
		}
	}

	final protected function showDebugMessage($message)
	{
		if(APP_DEBUG_LEVEL == DEBUG_OFF)
		{
			throw new MVCException('ActionController::showDebugMessage() is only available in debug mode!');
		}

		$this->response->dialogTitle = 'Debug message';
		$this->response->dialogMessage = print_r($message, true);
		$this->response->dialogButtonLabel = 'Quarante-deux';
		$this->response->dialogButtonManialink = $this->request->getReferer();

		$this->response->resetViews();
		$this->response->registerDialog('dialogs', 'generic_dialog');
	}
}

/**
 * @package ManiaLib_MVC
 */
class ControllerNotFoundException extends MVCException {}

/**
 * @package ManiaLib_MVC
 */
class ActionNotFoundException extends MVCException {}

?>