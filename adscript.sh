#!/bin/sh
utime=$(date +%s)
echo "dn: cn=$1,ou=Users,dc=us-west-2,dc=compute,dc=internal
objectClass: top
objectClass: posixAccount
objectClass: inetOrgPerson
sn: $1
cn: $1
uid: $1
uidNumber: $utime
gidNumber: 500
homeDirectory: /home/$1
loginShell: /bin/bash
userPassword: $2" > /var/www/xtra/"$1".ldif