Installation
------------

in your wp-content/plugins folder
create folder "call-to-optimize"
copy all files
activate plugin
add calls to action
add widget to layout

Twitter
-------

Tracking Twitter conversions (this only tracks people who click on follow and weren't already following you):
1. Start by creating your follow button at http://twitter.com/about/resources/followbutton
2. Create a new call to action and switch the editor to HTML mode
3. Paste the Twitter follow button code from step 1
4. Save the call
5. Find the ID of the call. You can do this on the reports page or in the URL of the edit page (/wp-admin/post.php?post=<call-id>)
6. Below this, add the following:
```html
<script type="text/javascript">
twttr.events.bind('follow', function(event) {
  ctopt_track("http://<blog-url>/<blog-path>/?ctopt_track=<call-id>");
});
</script>
```
In the URL you'll need to replace the URL with your blog url and the call-id with the id of the call.
7. Update and you're done

Mailchimp
---------

Mailchimp signup tracking (tracks every one who receives the signup configuratin mail, but can be changed to track any one who clicks on the submit button):
1. Again it's best to work in HTML mode when entering the data
2. In Mailchimp, create an embedded signup form: Lists > Choose your list > Signup embed form > choose your options, but do not disable JavaScript
3. Copy the code into a new call
3a. Note: by default the latest version of WordPress comes with jQuery in "no conflict" mode. This is not compatible with the Mailchimp signup form. To fix this, you need to replace every occurence of $ in the form with jQuery (capitalization is important)
4. Find the following text in the call: "function mce_success_cb(resp)" It is in the lower part of the signup code
5. A few lines lower you should see "if (resp.result=="success"){
6. Just below this line add the tracking code:
```javascript
if (resp.result=="success") {
  ctopt_track("http://<blog-url>/<blog-path>/?ctopt_track=<call-id>");
```
7. Save the changes

