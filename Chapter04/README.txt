Cacti User Import Script
Book: "Cacti 0.8.7 Designing NOC,Beginner's Guide"
Chapter 4 - "User Management"
Version 1.0
Based on the copy_user.php script

The Cacti User Import Script import users from a text file into cacti
using a template user for the default user settings and permission.

The Text file needs to be in the following format:

username;Full Name
username2;Full Name2

With each user being added as a separate line.

The import script is called with the following syntax:

php import_user.php <import file> <template user> <realm id>

Where <import file> is the file containing the users,

<template user> is the existing cacti user the new users should be based on. 
The template user needs to be set for the Local realm.

<realm id> is the authentication realm used for these users.
<realm id> can one of the following numbers: 0,1,2 
with 0 -> Local, 1 -> LDAP, 2 -> Web Basic

Existing users will be skipped.