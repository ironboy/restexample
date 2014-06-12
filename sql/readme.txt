*** dbquestions is a small library from Nodebite AB
*** that lets you use a json format to store
*** mysql questions and what query parameters should
*** be used in them
*** and returns the result as json...

*** the aim is to let you collect all your database
*** queries in one file and stay clear of php coding 
*** altogether

*** usage example:

*** in the file dbquestions.json do the following
*** create valid json starting with a json object,
*** specifying host, database, username and password

{
  "host": "localhost",
  "database": "books",
  "username" : "root",
  "password":"",
  
    *** specify each query by as a property
    *** where the value is the sql question
    *** embedded in a json array:

  "getBooksByTitle" : [
    *** in the array start writing the SQL question
    *** as a string element in the array:
    "SELECT * FROM booktitles WHERE title =",
    *** for each query parameter you want to include
    *** make a new array element
    *** that in itself is an array
    *** if the parameter should be treated as a string
    *** make a inner array as well
    [["title"]]
  ],

  *** if you query the php script like this:
  *** myserver/myapp/sql?action=getBookByTitle&title=Harry
  *** the question will build as
  *** SELECT * FROM boktitles WHERE title = "Harry"

  *** Please note: The query does not have to be with GET
  *** you can use POST as well!
  *** See further below for a example of a jQuery syntax!!!

  "getBooksByAuthor" : [
    "SELECT * FROM booktitles WHERE author =",
    [["author"]]
  ],

  "getBookByISBN" : [
    "SELECT * FROM booktitles WHERE isbn =",
    [["isbn"]]
  ],

  "autoComplete" : [
    "SELECT * FROM booktitles WHERE isbn LIKE",
    [["%","fragment","%"]],
    "OR author LIKE",
    [["%","fragment","%"]],
    "OR title LIKE",
    [["%","fragment","%"]]
  ],

  "updateBookTitle" : [
    *** normally an update or insert would
    *** return an empty query
    "UPDATE booktitles SET title =",
    [["title"]],
    "WHERE isbn=",
    [["isbn"]],
    *** but you can use semicolon
    *** to start another SQL statement
    *** and then it is the result from
    *** this statement that will be returned:
    ";SELECT * FROM booktitles WHERE isbn=",
    [["isbn"]]
  ]
}

*** jQuery example of a query:
$.ajax({
  url:"sql",
  cache:false,
  data: {
    action: "getBooksByAuthor",
    author: "Paul Taylor"
  },
  success:function(x){
    console.log(x);
  },
  error:function(x){
    console.log(x.responseJSON);
  }
});


*** paste everything below this line into dbquestions.json to test
*** the system and do not forget to import the example database

{
  "host": "localhost",
  "database": "books",
  "username" : "root",
  "password":"",
  
  "getBooksByTitle" : [
    "SELECT * FROM booktitles WHERE title =",
    [["title"]]
  ],

  "getBooksByAuthor" : [
    "SELECT * FROM booktitles WHERE author =",
    [["author"]]
  ],

  "getBookByISBN" : [
    "SELECT * FROM booktitles WHERE isbn =",
    [["isbn"]]
  ],

  "autoComplete" : [
    "SELECT * FROM booktitles WHERE isbn LIKE",
    [["%","fragment","%"]],
    "OR author LIKE",
    [["%","fragment","%"]],
    "OR title LIKE",
    [["%","fragment","%"]]
  ],

  "updateBookTitle" : [
    "UPDATE booktitles SET title =",
    [["title"]],
    "WHERE isbn=",
    [["isbn"]],
    ";SELECT * FROM booktitles WHERE isbn=",
    [["isbn"]]
  ]
}