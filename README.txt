The Chat 2.0.2 by Nate Baker (maplehaxx.com)

If you find any bugs email me at: nate[at]maplehaxx.com

SETUP:

database: "chat"
	table: "messages"
		field "msg":varchar(500)

	table: "online"
		field "user":varchar(20)

	table: "users"
		field "user":varchar(50)
		field "rights":int(10)
		field "nick":varchar(50)
		field "banned":int(10)
		field "muted":int(10)

	table: "misc"
		field "topic":varchar(500)


Put your accounts into "users" where:

"user" = their username
"rights" = their rights (3=admin,2=mod,1=regular)
"nick" = their nickname
"banned" = their banned status (1=banned, 0=not banned)
"muted" = their muted status (1=muted, 0=not muted)

Put those accounts into the .htpasswd file in the format:

user:pass

Seperated by newlines


Also, make sure to change the SQL login credentials to your own.


Enjoy!

------------------------------------------------------------------
