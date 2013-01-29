2.0.1
========================

MCN\Object\Entity\Repository

* Added a new expression <field>:null = true/false
* Added a new method for counting rows

2.0.0
========================
Removed all legacy code and stuff that no longer belongs in the system, and updated the ChangeLog to use markdown

MCN\Object\Entity\Repository

* supports sorting on relations as well as the root entity.
* supports wrapping fields in with functions by using | to suffix with the method name. To example 'name|length:gte' => 5 translates to LENGTH(name) > 5

1.0.3
========================
Set the validator MCN\Validator\ObjectExists to be not shared by default.
And removed the cloning requirements for it to work.

Fixed a bug in the Http controller plugin for get sort from query

Replaced the pagination counter with doctrines, gave a performance boost of 300%

1.0.2
========================
Create a class for namingconvetions in Stdlib

1.0.1
========================
Created a new class to contain our standard naming convention

1.0.0
========================
Initial stable version of the project