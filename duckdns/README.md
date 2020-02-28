# Duck DNS

Duck DNS is a free dynamic DNS hosted on AWS.
 
In fact, it helps to point a DNS (sub domains of [duckdns.org](https://www.duckdns.org/)) to an IP of your choice.

## Dynamic DNS service

It's a handy way to refer to a server/router with an easily rememberable name, where the servers IP address is likely to change.
When the router reconnects or ec2 server reboots, its IP address is set by the provider of that connection.
It means it may update at any time.

For the purpose of this project, the usage of Duck DNS is very handy as the Raspberry Pi is set on a DDNS environment.

## How to use

Simply run the script with the token set in argument: ´sudo ./duck.sh h3r3-1s-duckdns-t0k3n´.

It will return the token entered and then launch the process to connect to the Duck DNS service implemented.
