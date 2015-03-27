# ==============================================================================
# Conductor: A docker task runner
# ==============================================================================
FROM fedora:latest
MAINTAINER Brad Jones <brad@bjc.id.au>

# Update Fedora
RUN yum -y update && yum clean all

# Install Packages
RUN yum -y install \
	wget \
	git \
	npm \
	openssh \
	rsync \
	curl \
	php-cli \
	php-json \
	php-phar \
	php-ctype \
	php-openssl \
	php-curl \
	php-zip \
	&& yum clean all

# Probably not the greatest idea to be downloading
# stuff into the root of the container, lets go somewhere else.
WORKDIR /tmp

# Install Composer
RUN php -r "readfile('https://getcomposer.org/installer');" | php \
	&& chmod +x composer.phar \
	&& mv composer.phar /usr/local/bin/composer

# Install the standard robo task runner
# While I think my tweaked version of robo is better, this will give the user
# the option to run the standard version if they wish. They would just need
# to overide the entrypoint.
RUN wget http://robo.li/robo.phar \
	&& chmod +x robo.phar \
	&& mv robo.phar /usr/local/bin/robo

# Add our container files
ADD ["container-files","/"]

# Setup our very own special robo task runner.
RUN cd /opt/brads-robo && chmod +x robo && composer install

# Create our project source mount point
# We expect a RoboFile to exist in here at a minimum.
VOLUME ["/mnt/src"]

# Throw the user straight at the robo task runner
ENTRYPOINT ["/opt/brads-robo/robo"]
