FROM nginx:latest

RUN apt-get update
RUN apt-get install git -y

COPY ./default.conf /etc/nginx/conf.d/default.conf

COPY --chmod=0755 80-copy.sh /docker-entrypoint.d

EXPOSE 80

ENTRYPOINT ["/docker-entrypoint.d/80-copy.sh"]
