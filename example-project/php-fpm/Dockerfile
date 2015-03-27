FROM fedora:latest
MAINTAINER Brad Jones <brad@bjc.id.au>
RUN yum -y update && yum clean all
RUN yum -y install php-fpm && yum clean all
ADD ["container-files","/"]
EXPOSE 9000
CMD ["/usr/sbin/php-fpm"]
