<?php 

    // CLASS AND OBJECT
    class demo  
    {  
        private $a= "hello javatpoint";  
        public $b = "Hey";
        public function display()  
        {  
            echo $this->a.PHP_EOL;  
        }  
    }  
    $obj = new demo();  
    $obj->display();
    echo $obj->b;
    // var_dump($obj); gives the detailed explation of the obj


    // ABSTRACT CLASS

    abstract class a  
    {  
        public $name,$age;
        public function Describe()  
        {  
                return $this->name . ", " . $this->age . " years old";      
        }  
        abstract public function dis1();  
        abstract public function dis2();  
    }  
    class b extends a  
    {  
        public function dis1()  
            {  
                echo "javatpoint";  
            }  
        public function dis2()  
            {  
                echo "SSSIT";     
            }  
    }

    // access specifiers : public, private, protected, default

    // CONSTRUCTOR
    class Example  
    {  
        public $name;
        public function __construct($name)  
        {  
            $this->name = $name;
            echo "Hello javatpoint";  
        }  
    }  
    $obj = new Example($name);  
    $obj->name;

    // DESTRUCTOR
    class demoo 
    {  
        public function demoo()  
        {  
            echo "constructor1...";  
        }  
    }  
    class demo1 extends demoo 
    {  
        public function __construct()  
        {  
            echo parent::demoo();  
            echo "constructor2...";  
        }  
        public function __destruct()  
        {  
            echo "destroy.....";  
        }  
    }  
    $obj= new demo1();
    // output : constructor1...constructor2...destroy.....

    // final : function with final can't be overriden
    // final function name(){}

    // inheritance



    // funcitons in oops
    echo get_class($obj);  // to get the class of obj
    print_r(get_class_vars("cls1")); // to get the obj of class
    print_r(get_class_methods("cls1")); // methods of class
    print_r(get_class_methods("cls1")); // to get all declared classes
    print_r(get_object_vars($obj)); //all vars of an obj
    echo class_exists("cls1");  //returns boolean 1 : class exits, 0: not exits
    echo is_subclass_of("cls2","cls1");  // is cls2 sub class of cls1
    echo method_exists("cls1","fun1");  // method exits in a class
    
    // inheritance
    class d  
    {  
        function fun1()  
        {  
            echo "javatpoint";  
        }  
    }  
    class e extends d 
    {  
        function fun2()  
        {  
            echo "SSSIT";  
        }  
    }  
    $obj= new e();  
    $obj->fun1();  // prints SSSIT

    interface x
    {  
        public function dis1();  
    }  
    interface y  
    {  
        public function dis2();  
    }  
  
    class z implements x,y  
    {  
        public function dis1()  
        {  
            echo "method 1...";  
        }  
        public function dis2()  
        {  
            echo "method2...";  
        }  
    }  
    $obj= new z();  
    $obj->dis1();  // method 1
    $obj->dis2();  // method 2

    // overloading
    class Exmp
    {  
        public $name,$age;
        public function __constructWithName($name)  
        {  
            $this->name = $name;
            echo "Hello javatpoint";  
        }
        public function __constructWithNameAndAge($name,$age){
            $this->name = $name;
            $this->age = $age;
        }
        public static function createWithName($name)
        {
            return new self($name);
        }

        public static function createWithNameAndAge($name, $age)
        {
            return new self($name, $age);
        }
    }
    $obj1 = Exmp::createWithName("John");
    $obj2 = Exmp::createWithNameAndAge("Alice", 25);

    // TRAITS
    // The class can extend only one class, if they need to extend more than one class then we can use multiple inheritence
    
    class parentClass {  
        public function parentFunction() {  
           echo " Hello this is a parent class \n";  
        }  
      }  
         
      trait forParentClass {  
        public function traitFunction() {  
           echo " and this is trait \n";  
        }  
      }  
          
      class childClass extends parentClass {  
         use forParentClass;  
         public function childFunction() {  
            echo " this is the child class which inherit parent using trait \n";  
        }   
      }  
          
      $obj = new childClass();  
      $obj->parentFunction();  
      $obj->traitFunction();  
      $obj->childFunction();  

?>