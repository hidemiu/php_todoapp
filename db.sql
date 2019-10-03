mysql -u root -p

create database php_todoapp;
create user dbuser@localhost identified by 'dbuser';
grant all on php_todoapp.* to dbuser@localhost;

use php_todoapp

create table todos (
  id int not null auto_increment primary key,
  state tinyint(1) default 0, /* 0:not finished, 1:finished */
  title text
);

insert into todos (state, title) values
(0, 'todo 0'),
(0, 'todo 1'),
(1, 'todo 2');

GRANT ALL ON *.* to root@'192.168.33.11'; 
GRANT ALL ON *.* to dbuser@'192.168.33.11' IDENTIFIED BY 'dbuser'; 