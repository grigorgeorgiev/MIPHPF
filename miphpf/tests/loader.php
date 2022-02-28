<?php
	require_once('../include/miphpf/Init.php');
	require_once('header.html');
	
	/**
	 * Loader tester class
	 */
	class miLoaderTester extends miLoader {
		public function doTest()
		{
			foreach (array_keys(self::$_classMap) as $className) {
				echo 'Autoloading ' . $className . '<br>';
				__autoload($className);
			}
		}
	}
	
	echo 'Testing miLoader<br>';
	$loadTester = new miLoaderTester;
	$loadTester->doTest();
	echo 'Test completed<br>';
	
	require_once('footer.html');
?>