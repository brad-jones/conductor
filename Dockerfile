# ==============================================================================
# Conductor: A docker task runner
# ==============================================================================
FROM alpine:latest
MAINTAINER Brad Jones <brad@bjc.id.au>

# Docker Cache Buster
# This should reflect the current stable version.
# By bumping this we ensure we get the latest versions of everything.
ENV CONDUCTOR_VERSION="v0.0.1"

# Install Packages
RUN apk add --update \
	git \
	php \
	php-json \
	php-phar \
	php-ctype \
	php-openssl \
	php-curl \
	php-zip \
	&& rm -rf /var/cache/apk/*

# Install Composer
RUN php -r "readfile('https://getcomposer.org/installer');" | php \
	&& chmod +x composer.phar \
	&& mv composer.phar /usr/local/bin/composer

# Install the robo task runner
RUN wget http://robo.li/robo.phar \
	&& chmod +x robo.phar \
	&& mv robo.phar /usr/local/bin/robo

# Install our very own special robo task runner
# This is setup as the default entry point but can easily be overidden
ADD ["container-files","/"]
RUN chmod +x /opt/brads-robo/robo
RUN cd /opt/brads-robo && composer install -vvv

# Create our project source mount point
# We expect a RoboFile to exist in here at a minimum.
# eg: docker run -it --rm -v /project/path:/mnt/src bradjones/conductor foo:task
RUN mkdir /mnt/src

# Throw the user straight at the robo task runner
ENTRYPOINT ["/opt/brads-robo/robo"]
