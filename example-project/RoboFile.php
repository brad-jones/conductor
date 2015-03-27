<?php
/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 *
 * You may specfically like to look at http://robo.li/tasks/Docker/
 *
 * Also a couple of extra items to note:
 *
 *    - This RoboFile will be run inside the conductor container.
 *
 *    - Conductor has read/WRITE access to this folder via `/mnt/src`.
 *
 *    - Conductor can not access anything else on your host system.
 *
 *    - Conductor has read/WRITE access to docker running on your host.
 *
 *    - This particular example uses my modded version of Robo.
 *      https://github.com/brad-jones/robo-tasks
 */
class RoboFile extends Brads\Robo\Tasks
{
	/**
	 * Lets build all our containers.
	 */
	public function build()
	{
		// we could get fancy with version/build numbers
		// but for this example we just use the latest tag.
		$this->taskDockerBuild('nginx')->tag('example-project/nginx:latest')->run();
		$this->taskDockerBuild('php-fpm')->tag('example-project/php-fpm:latest')->run();
		$this->taskDockerBuild('storage')->tag('example-project/storage:latest')->run();
		
		// or maybe we just need to pull an image down from the docker hub
		// $this->taskDockerPull('mysql')->run();
	}
	
	/**
	 * Now lets run our containers and link them together in the correct order.
	 */
	public function run()
	{
		// Robo does not yet have a built in task for `docker create`
		// Brads Robo Tasks might fix that :)
		$this->taskExec('docker')
			->arg('create')
			->option('name', 'example-project-shared-storage')
			->arg('example-project/storage:latest')
		->run();
		
		// Start php-fpm
		$php = $this->taskDockerRun('example-project/php-fpm:latest')
			->name('example-project-php-fpm')
			->option('volumes-from', 'example-project-shared-storage')
			->option('restart', 'on-failure:10')
			->detached()
		->run();
		
		$this->say("Started php-fpm container: ".$php->getCid());
		
		// Start nginx
		$nginx = $this->taskDockerRun('example-project/nginx:latest')
			->name('example-project-nginx')
			->option('volumes-from', 'example-project-shared-storage')
			->option('restart', 'on-failure:10')
			->link($php, 'example-project-php-fpm')
			->publish(8081, 80)
			->detached()
		->run();
		
		$this->say("Started nginx container: ".$nginx->getCid());
		$this->say('Go to http://localhost:8081/');
	}
	
	/**
	 * Starts our containers using the config defined in the run command.
	 */
	public function start()
	{
		$this->taskDockerStart('example-project-php-fpm')->run();
		$this->taskDockerStart('example-project-nginx')->run();
	}
	
	/**
	 * Stops all our containers.
	 */
	public function stop()
	{
		$this->taskDockerStop('example-project-php-fpm')->run();
		$this->taskDockerStop('example-project-nginx')->run();
	}
	
	/**
	 * Removes all our containers.
	 */
	public function remove($opts = ['destroy-data' => false])
	{
		// NOTE: There appear to be a bug with $this->taskDockerRemove()
		
		// This is just an example and while testing I want to delete the shared
		// volume  container on every reload however in a real project,
		// especially on the production environment you may instead wish to do
		// something like this example.
		
		/*if ($opts['destroy-data'])
		{
			$this->taskDockerRemove('example-project-shared-storage')->run();
		}*/
		
		$this->taskExec('docker rm example-project-shared-storage')->run();
		$this->taskExec('docker rm example-project-php-fpm')->run();
		$this->taskExec('docker rm example-project-nginx')->run();
	}
	
	/**
	 * Lets run it all together.
	 */
	public function reload()
	{
		$this->stop();
		
		$this->remove();
		
		$this->build();
		
		$this->run();
	}
	
	/**
	 * Now lets push our containers to a remote server.
	 */
	public function deploy($to = 'staging')
	{
		/*
		 * This where things can get even cooler, I am not going to bother
		 * with any actual working example here because well this is just an
		 * example. I will leave it to your skills to create this bit.
		 *
		 * A few thoughts though:
		 *
		 *    - First of all check out http://robo.li/tasks/Remote/
		 *
		 *    - Because this is being run inside a docker container we can alway
		 *      rely on the SSH client being available. Unlike before where I
		 *      had to cater for people running on Windows and resorted to
		 *      using: http://phpseclib.sourceforge.net/ Not that I have
		 *      anything against phpseclib, it's a great lib and may still have
		 *      it's use case, even in a docker environment.
		 *
		 *    - Previously before docker I used Puppet heavily and so will
		 *      probably continue to use Puppet to configure my staging and
		 *      production hosts. Just rsync up the puppet config and apply it.
		 *
		 *    - By far the easiest method of actually deploying your images.
		 *      Will be to signup for some private docker image hosting or
		 *      run your own in house docker hub.
		 *
		 *    - However one could just as easily script the deployment using
		 *      docker save, docker export, docker import, docker load commands.
		 *
		 *    - Or you could just git clone your source and build on the
		 *      production host. For the most part this would probably work fine
		 *      and be much faster than uploading GBs of images. However then
		 *      your containers arn't truely immutable, you would have to much
		 *      more explict about the versions of software you installed into
		 *      your container.
		 */
	}
}
