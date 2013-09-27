PHP-Container
=============

The PHP-Container is a dependency injection container to be used in PHP.  It behaves similar to that of Spring, if you're familiar with Java.  It allows a developer to retrieve a constructed object from the container with all of its dependencies already injected.


Example Usage
-------------

Say you have a UserRepository class that handles all CRUD operations (Create, Retrieve, Update, Delete) for a User class.  Obviously, this is going to need a Database, or somewhere to actually operate.  This could be a MySQL, Postgres, or Oracle database.  Or, it could just be in memory.  Regardless, you need a Database.  Your repo may look like this...

    <?php
      class UserRepository {

        private $db;

        public function create(User $user) { ... }
        public function delete(User $user) { ... }
        public function retrieve($userId) { ... }
        public function update(User $user) { ... }

        public function setDb(Database $db) {
          $this->db = $db;
        }
      }
    ?>

In this case, the repo has a need for a Database, which is an interface for whatever type of Database you want to use.  Now, if I just want a repo, why do I need to do the injections myself?  I just want to get a repo and already know the $db variable has already been set.  That's where the container comes into place.

With the container, I can specify which implementation of Database is the one to be used, all by using annotations in the PHPDoc for the class and variable.  Here's what my repo and database may look like now...

    <?php
      /**
       * An implementation of a UserRepo to handle CRUD operations.
       * @ManagedClass
       * @SharedClass
       */
      class UserRepository {

        /**
         * @Autowired
         */
        private $db;

        public function create(User $user) { ... }
        public function delete(User $user) { ... }
        public function retrieve($userId) { ... }
        public function update(User $user) { ... }

        public function setDb(Database $db) {
          $this->db = $db;
        }
      }
    ?>

The @ManagedClass attribute tells the container (when it does it's scans) to take note of this class.  If a class does not have this attribute, the container ignores it.

The @SharedClass attribute tells the container that this class should serve as a Singleton... meaning that there should only be one implementation of it in use.  In this case, we don't need lots of versions of a UserRepository, but we may want to have multiple instances of a User class.

The @Autowired attribute tells the container that whenever asks for an object of this class, this dependency should be fulfilled.



Now, in my database implementation, I can hook it up by using these attributes...

    <?php
      /**
       * Implementation of Database using a MySQL database.
       * @ManagedClass("db")
       */
      class MysqlDatabase implements Database {
        // Code goes here
      }
    ?>

In this case, I'm using the @ManagedClass attribute, but I'm providing the name for this class.  Now, whenever we ask for any objects from the container that has a @Autowired variable named $db, this will be injected.  You can also use a parameter for the @Autowired.  If we wanted to name the $db variable something like $databaseObj, we can use @Autowired("db") to let the container know we want a "db" class to be inserted for the $databaseObj variable.  Cool, huh?

So, now that we have everything wired together, all we have to do is ask the container for a UserRepository.  Here you go...

    <?php
      $repo = Container::getInstance()->userRepo;
    ?>

That's it!  You get a UserRepository that already has the $db variable provided.  You don't have to do that!


Why use PHP-Container?
----------------------

So, why do this?  Well, if you wish to swap out your Database implementation (say you're changing server`s, etc.), all you have to do is change a single attribute.  You don't have to go into the constructors or all over your code to find where you provided the database yourself.  It's very simple!

It also makes your code much cleaner without having a lot of initialization stuff all over the place!

