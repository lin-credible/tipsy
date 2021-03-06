<?php

// class for library controller test
class LibraryController extends Tipsy\Controller {
	public function init($args = []) {
		echo 'LIBRARY';
	}
}

// class for instance controller test
class InstanceController extends Tipsy\Controller {
	public function init($args = []) {
		echo 'INSTANCE';
	}
}

class LibraryControllerParent extends Tipsy\Controller {
	public function init($args = []) {
		parent::init($args);
		echo 'LIBRARY';
	}
}


class RouterTest extends Tipsy_Test {

	public function setUp() {
		$this->tip = new Tipsy\Tipsy;
		$this->useOb = true; // for debug use
	}

	public function testRouterBasic() {
		$_REQUEST['__url'] = 'router/basic';
		$this->ob();

		$this->tip->router()
			->when('router/basic', function() {
				echo 'BASIC';
			})
			->when('router/notbasic', function() {
			})
			->otherwise(function() {
			});
		$this->tip->start();

		$check = $this->ob(false);
		$this->assertEquals('BASIC', $check);
	}

	public function testRouterOtherwise() {
		$_REQUEST['__url'] = 'router/basic';
		$this->ob();

		$this->tip->router()
			->otherwise(function() {
				echo 'OTHER';
			});
		$this->tip->start();

		$check = $this->ob(false);
		$this->assertEquals('OTHER', $check);
	}

	public function testRouterBasicAlternate() {
		$this->tip->router()
			->when('router/basic', function() {
				echo 'BASIC';
			});
		$this->ob();
		$this->tip->start('   //router/basic // ');
		$this->assertEquals('BASIC', $this->ob(false));
	}

	public function testRouterHomeSuccess() {
		$_REQUEST['__url'] = '/';

		$this->tip->router()
			->home(function() {
				echo 'HOME';
			});

		$this->ob();
		$this->tip->start();

		$this->assertEquals('HOME', $this->ob(false));
	}

	public function testRouterHomeSuccessAgain() {
		$_REQUEST['__url'] = '';

		$this->tip->router()
			->home(function() {
				echo 'HOME';
			});

		$this->ob();
		$this->tip->start();

		$this->assertEquals('HOME', $this->ob(false));
	}

	public function testRouterId() {
		$_REQUEST['__url'] = 'router/file/BACON';

		$this->ob();

		$this->tip->router()
			->when('router/file/:id', function($Params) {
				echo $Params->id;
			});
		$this->tip->start();

		$check = $this->ob(false);
		$this->assertEquals('BACON', $check);
	}

	public function testRouterIdSub() {
		$_REQUEST['__url'] = 'router/file/BACON/eat';

		$this->ob();

		$this->tip->router()
			->when('router/file/:id/eat', function() {
				echo 'SUB';
			});
		$this->tip->start();

		$check = $this->ob(false);
		$this->assertEquals('SUB', $check);
	}

	public function testRouterLibraryController() {
		$_REQUEST['__url'] = 'router/library';

		$this->ob();

		$this->tip->router()
			->when('router/library', [
				'controller' => 'LibraryController'
			]);
		$this->tip->start();

		$check = $this->ob(false);
		$this->assertEquals('LIBRARY', $check);
	}

	public function testRouterInternalController() {
		$_REQUEST['__url'] = 'router/internal';
		$this->tip->controller('InternalController', function() {
			echo 'INTERNAL';
		});

		$this->ob();

		$this->tip->router()
			->when('router/internal', [
				'controller' => 'InternalController'
			]);
		$this->tip->start();

		$check = $this->ob(false);
		$this->assertEquals('INTERNAL', $check);
	}

	public function testRouterInstanceController() {
		$_REQUEST['__url'] = 'router/instance';
		$test = new InstanceController;

		$this->ob();

		$this->tip->router()
			->when('router/instance', [
				'controller' => $test
			]);
		$this->tip->start();

		$check = $this->ob(false);
		$this->assertEquals('INSTANCE', $check);
	}

	public function testRouterHomeOne() {
		$_REQUEST['__url'] = '';

		$this->ob();

		$this->tip->router()
			->when('', function() {
				echo 'ONE';
			});
		$this->tip->start();

		$check = $this->ob(false);

		$this->assertEquals('ONE', $check);
	}

	public function testRouterHomeTwo() {
		$_REQUEST['__url'] = '/';

		$this->ob();

		$this->tip->router()
			->when('', function() {
				echo 'TWO';
			});
		$this->tip->start();

		$check = $this->ob(false);

		$this->assertEquals('TWO', $check);
	}

	public function testRouterHomeThree() {
		$_REQUEST['__url'] = '';

		$this->ob();

		$this->tip->router()
			->when('/', function() {
				echo 'THREE';
			});
		$this->tip->start();

		$check = $this->ob(false);

		$this->assertEquals('THREE', $check);
	}

	public function testRouterHomeFour() {
		$_REQUEST['__url'] = '/';

		$this->ob();

		$this->tip->router()
			->when('/', function() {
				echo 'FOUR';
			});
		$this->tip->start();

		$check = $this->ob(false);

		$this->assertEquals('FOUR', $check);
	}


	public function testRouterError() {
		$_REQUEST['__url'] = 'router/errorme';

		$this->ob();

		$this->tip->router()
			->otherwise(function() {
				echo '404';
			});
		$this->tip->start();

		$check = $this->ob(false);
		$this->assertEquals('404', $check);
	}

	public function testHttpGetSuccess() {
		$_REQUEST['__url'] = 'router/get';
		$_SERVER['REQUEST_METHOD'] = 'GET';

		$this->tip->router()
			->get('router/get',function() {
				echo 'YES';
			})
			->otherwise(function() {
				echo 'NO';
			});

		$this->ob();
		$this->tip->start();
		$check = $this->ob(false);
		$this->assertEquals('YES', $check);
	}

	public function testHttpGetFail() {
		$_REQUEST['__url'] = 'router/get';
		$_SERVER['REQUEST_METHOD'] = 'POST';

		$this->tip->router()
			->get('router/get',function() {
				echo 'YES';
			})
			->otherwise(function() {
				echo 'NO';
			});

		$this->ob();
		$this->tip->start();
		$check = $this->ob(false);
		$this->assertEquals('NO', $check);
	}

	public function testHttpPostSuccess() {
		$_REQUEST['__url'] = 'router/post';
		$_SERVER['REQUEST_METHOD'] = 'POST';

		$this->tip->router()
			->post('router/post',function() {
				echo 'YES';
			})
			->otherwise(function() {
				echo 'NO';
			});

		$this->ob();
		$this->tip->start();
		$check = $this->ob(false);
		$this->assertEquals('YES', $check);
	}

	public function testHttpPostFail() {
		$_REQUEST['__url'] = 'router/post';
		$_SERVER['REQUEST_METHOD'] = 'GET';

		$this->tip->router()
			->post('router/post',function() {
				echo 'YES';
			})
			->otherwise(function() {
				echo 'NO';
			});

		$this->ob();
		$this->tip->start();
		$check = $this->ob(false);
		$this->assertEquals('NO', $check);
	}

	public function testHttpGetParam() {
		$_REQUEST['__url'] = 'router/get';
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_GET['test'] = 'YES';

		$this->tip->router()
			->get('router/get',function($Request) {
				echo $Request->test;
			});

		$this->ob();
		$this->tip->start();
		$check = $this->ob(false);
		$this->assertEquals('YES', $check);
	}


	public function testHttpPostParam() {
		$_REQUEST['__url'] = 'router/post';
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_POST['test'] = 'YES';

		$this->tip->router()
			->post('router/post',function($Request) {
				echo $Request->test;
			});

		$this->ob();
		$this->tip->start();
		$check = $this->ob(false);
		$this->assertEquals('YES', $check);
	}

	public function testArraySetup() {
		$_REQUEST['__url'] = 'router/array';
		$_SERVER['REQUEST_METHOD'] = 'POST';

		$this->tip->router()
			->when([
				'route' => 'router/array',
				'method' => 'post,put',
				'controller' => function() {
					echo 'ARRAY';
				}
			]);

		$this->ob();
		$this->tip->start();
		$check = $this->ob(false);
		$this->assertEquals('ARRAY', $check);
	}

	public function testRouteLoop() {
		$_REQUEST['__url'] = 'loop';
		$_SERVER['REQUEST_METHOD'] = 'POST';

		$this->tip->router()
			->post('loop',function() {
				echo 'ONE';
			})
			->when('loop',function() {
				echo 'TWO';
			});

		$this->ob();
		$this->tip->start();
		$check = $this->ob(false);
		$this->assertEquals('TWO', $check);
	}

	public function testRouterException() {
		$_REQUEST['__url'] = 'router/exception';

		try {
			$this->tip->start();
			$caught = false;
		} catch (Exception $e) {
			$caught = true;
		}

		$this->assertTrue($caught);
	}

	public function testRouterNullException() {
		$_REQUEST['__url'] = 'router/exception';

		try {
			$this->tip->router()->when();
			$caught = false;
		} catch (Exception $e) {
			$caught = true;
		}

		$this->assertTrue($caught);
	}

	public function testInvalidRoute() {
		try {
			$this->tip->router()
				->when(null,null);
		} catch (Exception $e) {
			$catch = $e->getMessage();
		}

		$this->assertEquals('Invalid route specified.', $catch);
	}

	public function testInvalidArrayRoute() {
		try {
			$this->tip->router()
				->when([
					'method' => 'post,put',
					'controller' => function() {
						echo 'ARRAY';
					}
				]);
		} catch (Exception $e) {
			$catch = $e->getMessage();
		}

		$this->assertEquals('Invalid route specified.', $catch);
	}

	public function testInvalidControllerClass() {
		$_REQUEST['__url'] = 'fail';

		$this->tip->router()
			->when('fail','ClassFail');

		$this->ob();
		try {
			$this->tip->start();
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		$check = $this->ob(false);

		$this->assertEquals('No controller attached to route.', $check);
	}

	public function testRouterParse() {
		$_SERVER['REQUEST_URI'] = '/router/parse?test=a';
		$_SERVER['SCRIPT_NAME'] = '/app/index.php';

		$this->ob();

		$this->tip->router()
			->when('router/parse', function() {
				echo 'BASIC';
			});
		$this->tip->start();

		$check = $this->ob(false);
		$this->assertEquals('BASIC', $check);
	}

	public function testRouterPath() {
		$_SERVER['TEST'] = 1;
		$_SERVER['REQUEST_URI'] = '/RouterTest.php/router/path?test=a';
		$_SERVER['SCRIPT_NAME'] = '/RouterTest.php';

		$this->ob();

		$this->tip->router()
			->when('router/path', function() {
				echo 'BASIC';
			});
		$this->tip->start();

		$check = $this->ob(false);
		$this->assertEquals('BASIC', $check);
	}

	public function testRouterAlias() {
		$_REQUEST['__url'] = 'item/1/edit';

		$this->ob();

		$this->tip->router()
			->when('item/edit/:id', function($Params) {
				echo $Params->id;
			})
			->alias('something', 'item/edit/:id')
			->alias('item/:id/edit', 'item/edit/:id')
			->otherwise(function() {
				echo 'OTHER';
			});
		$this->tip->start();

		$check = $this->ob(false);
		$this->assertEquals('1', $check);
	}

	public function testRouterLibraryControllerParent() {
		$_REQUEST['__url'] = 'router/library';

		$this->ob();

		$this->tip->router()
			->when('router/library', [
				'controller' => 'LibraryControllerParent'
			]);
		$this->tip->start();

		$check = $this->ob(false);
		$this->assertEquals('LIBRARY', $check);
	}

	public function testRouterShorthandGet() {
		$_REQUEST['__url'] = 'router/shorthand';
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$this->tip->get('router/shorthand', function() use (&$res) {
			$res = true;
		});
		$this->tip->start();
		$this->assertTrue($res);
	}

	public function testRouterShorthandPost() {
		$_REQUEST['__url'] = 'router/shorthand';
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$this->tip->post('router/shorthand', function() use (&$res) {
			$res = true;
		});
		$this->tip->start();
		$this->assertTrue($res);
	}

	public function testRouterShorthandWhen() {
		$_REQUEST['__url'] = 'router/shorthand';
		$this->tip->when('router/shorthand', function() use (&$res) {
			$res = true;
		});
		$this->tip->start();
		$this->assertTrue($res);
	}

	public function testRouterShorthandDelete() {
		$_REQUEST['__url'] = 'router/shorthand';
		$_SERVER['REQUEST_METHOD'] = 'DELETE';
		$this->tip->delete('router/shorthand', function() use (&$res) {
			$res = true;
		});
		$this->tip->start();
		$this->assertTrue($res);
	}

	public function testRouterShorthandHome() {
		$_REQUEST['__url'] = '';
		$this->tip->home(function() use (&$res) {
			$res = true;
		});
		$this->tip->start();
		$this->assertTrue($res);
	}

	public function testRouterShorthandOtherwise() {
		$_REQUEST['__url'] = 'router/shorthand';
		$this->tip->otherwise(function() use (&$res) {
			$res = true;
		});
		$this->tip->start();
		$this->assertTrue($res);
	}

	public function testRegex() {
		$_REQUEST['__url'] = 'router/reg/ex';
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$this->tip->when('/^router\/.*\/ex$/',function() use (&$res) {
			$res = true;
		});
		$this->tip->start();
		$this->assertTrue($res);
	}

	public function testRegexSuffix() {
		$_REQUEST['__url'] = 'assets/app.scss';
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$this->tip->get('/\.scss$/',function() use (&$res) {
			$res = true;
		});
		$this->tip->start();
		$this->assertTrue($res);
	}
}
