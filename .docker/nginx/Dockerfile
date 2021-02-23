FROM ubuntu:12.04

RUN echo $HOME

WORKDIR /app

RUN apt-get update && apt-get install -y apt-utils nano vim wget curl net-tools dialog htop mc lsof bash nginx && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN usermod -u 1000 www-data

EXPOSE 80
CMD ["nginx"]
