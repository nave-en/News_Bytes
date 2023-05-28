# News_Bytes
Description
  At News Bytes, the marketing department often finds themselves using long URLs with UTM tracking. These URLs often travel in emails, getting copied over sheets and docs, thereby are at risk of getting ruined by formatters.


Approach
  Instead of using the full URL, we can use a tiny URL, which is easy to maintain and chances of less format table. The main use of the tiny URL is only for storing in the Excel sheets and document storing.
  We can create a table named “url_hashings” in the database for storing the tiny URL, the table contains the column's id, url, hashed_url, clicks_count, active and time stamps.
    id ⇾ This column is used to store the row count
    url ⇾ This column is used to store original url and of type text
    hashed_url ⇾ This column is used to store the tiny url (only the encrypt part) and column type is text.
    clicks_count ⇾ This column is used to store the no of users clicks to the url and the column type is Big Int.
    active ⇾ This column is used to store whether the url is in active state or not, and the column type is tiny int.
    created_at ⇾ this column stores time format when the row is created.
    updated_at ⇾ This column stores the time format when the row is lastly updated.


Create a tiny url for an url
When the user tries to create a tiny url for an url it calls the controller function then it does a basic validation of the request and once the validation is successfully completed and the tiny url will be generated, and it will be stored in the database. Then the tiny url will be sent to the user. For example
    Original url : "https://www.google.com"
    Tiny url : “https:://newsbytes.com/rd=709192385761”

But in db we store only the “709192385761” in the hashed_url, because before the rd part in the url is common for all url, note the hashed url will be unique, and it will not be common. For generating the tiny url we use md5 algorithm of fixed size 12.

Retrieve the original url
   When the user wants the original url, he can simply pass the tiny url to the server after the validation is successfully completed, the original URL will be sent to the user

Update the user clicks count
   When the user clicks on the url it will call the server function, and it will update the click_count column value by one.

Note : In the all modules there will be basic validation like url format, empty check and unique constraint for storing the data in the database. If any of the condition is failed, the url will not be processed and the details will be share to the user along with the failure of the reason. In some places we use log for storing the errors, and we use try catch block for catching the running the exception.

Reason for this Approach
   The main idea is used to store the uniqueness of the hashed url, because there will be only one hashed url for the original url. Because if we allow generating the multiple hashed url then it will be hard to maintain the data.

