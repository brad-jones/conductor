Conductor - A docker task runner, based on http://robo.li/
================================================================================
Basically this is a portable PHP environment that can
run ```robo``` anywhere ```docker``` can run.

Gone are the days of asking your fellow developers to install package ```X``` &
package ```Y```, oh and module ```Z``` before your awesome project will run on
their local workstation.

Now they just need ```docker```. Yep just 1 thing to install.
And it will work on the big 3 operating systems - Windows, Mac & Linux.
_Oh they might also need ```git``` but thats got to be a given these days..._

Installation
--------------------------------------------------------------------------------
Assuming you have ```docker``` already installed, its pretty straight forward:

	docker pull bradjones/conductor:latest

Then just grab a copy of [conductor](http://git.io/jCzW) and place it in the
root of your new fancy docker based project along with a ```RoboFile.php```

> For more info about docker see: http://www.docker.com/

Usage
--------------------------------------------------------------------------------
Firstly I assume you know what the robo task runner is,
if not go and see: http://robo.li/

Next I assume you have just created a brand new project and have added your
```RoboFile.php``` file to the root. For the sake of this example lets say the
file path to your new project is: ```/home/foo/projects/the_future```

The idea isn't to use the docker container directly, although you can if you
really really like typing by running a command that looks something like:

	docker run -it --rm \
	-v $(which docker):$(which docker) \
	-v /home/foo/projects/the_future:/mnt/src \
	bradjones/conductor:latest some:robocmd

Now I don't know about you but thats just silly stupid, I am way too lazy to
type that lot in everytime I want to run a robo task. Notice how it's only the
very last part of the command that actually calls a robo command, in the next
example you see it as one of the first arguments.

	./conductor some:robocmd

Okay so what has happened, how is this magic vodo making my command run.
```conductor``` is just a shell script that I added to the root of my new
project at ```/home/foo/projects/the_future```. It encapsulates all that extra
_"docker"_ stuff and makes it appear as though you are just calling an every day
normal plain jane command.

> If you have every used [Laravel](http://laravel.com)
> think of it like a new fancy version of ```artisan```

Because the ```conductor``` script is now part of my new project. And I have
just committed it and pushed it to github. When my fellow developer, lets call
him Mr Docker, first name What, middle name is... :)

When Mr Docker performs a ```git clone``` or a ```git pull``` on your new
project, all you have to say is go to https://docs.docker.com/installation/
and follow the instructions.

Then once you have docker installed you can run this command to get started:
```./conductor insert-your-awesome-robo-build-command-here```

Example Project
--------------------------------------------------------------------------------
Located at https://github.com/brad-jones/conductor/tree/master/example-project
is a complete working example of how all this might work. I have choosen to
create a basic multi container web server using nginx & php-fpm.

You can see how the ```RoboFile.php``` ties everything together.
It automates the entire process from build through to removal.

The RoboFile is heavily commented so I won't bother repeating myself here.

--------------------------------------------------------------------------------
Developed by Brad Jones - brad@bjc.id.au
