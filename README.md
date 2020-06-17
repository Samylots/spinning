# Spinning [de Beauce]
## College Final Project - 2016

This project is the final project for my "DEC". The project is a real one with an actual client that needed a website for it's spinning gym.
The client wanted a website to be able to plan sessions, courses, activities and memberships of it's clients. (Also managing trainers)

At the end, the client picked out the best website of them all. She picked two website since she loved them too much both. 
She picked the one that Samuel Fortin and Emmanuel Veilleux have done and the other one was mine.
We were doing it by teams of 2 students, but my class was odd so I had to do it alone.

## Steps to run it
1. Create a MySql database
2. Open the `.\php\objects\Config.php` file and change the following variables to match your databse:
`$DBHost`
`$DBName`
`$DBUser`
`$DBPassword`
3. Deploy the comple website to a running PHP environment (WAMP)
4. (You can also use the script `.\init\ScriptStructureAvecDonnees.sql` to already have some sessions, courses and users)
5. Open the website link.
Note: The first access will take a long time to response since it need to create the whole requires database when it's not yet there.
6. Now you can enjoy the website! (See "Espace membre")

For some obvious reasons, I won't share any credential to an admin account. _Sorry!_
I'll let you deploy it on your local environment and you will be able to edit the database user's record to make it as an administrator.

(Yes I know, most of the features are not documented anywhere.)
