FROM fedora:latest
MAINTAINER Brad Jones <brad@bjc.id.au>
RUN yum -y update && yum clean all
RUN yum -y install nginx && yum clean all
ADD ["container-files","/"]
EXPOSE 80
CMD ["/usr/sbin/nginx"]
