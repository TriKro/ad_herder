Installation
============

in your wp-content/plugins folder
create folder "call-to-optimize"
copy all files
activate plugin
add calls to action
add widget to layout

Integration
===========

By default, the plugin will monitor clicks on "a href" HTML links. So to monitor the conversion on those, you don't need to do anything. If you want to monitor different, more complicated widgets, you'll need to do some work yourself.

A conversion is monitored by calling (in JavaScript) the `ctopt_track()` function with as argument the monitoring URL. This url consists of the base url of your blog with the id of the call that the conversion was on. For instance, it might look like this:

    http://www.streamhead.com/?ctopt_track=123
    http://localhost/call-optimizer/?ctopt_track=9

To find the ID of the call you can open the reports page and check the table. The first column shows the id. Another method is to edit the call and look at the url: `/wp-admin/post.php?post=7&action=edit`. In this case, the id is 7.

Twitter
-------

Tracking Twitter conversions (this only tracks people who click on follow and weren't already following you):

1. Start by creating your follow button at http://twitter.com/about/resources/followbutton
2. Create a new call to action and switch the editor to HTML mode
3. Paste the Twitter follow button code from step 1
4. Save the call
5. Below this, add the following:

        <script type="text/javascript">
        twttr.events.bind('follow', function(event) {
          ctopt_track("http://<blog-url>/<blog-path>/?ctopt_track=<call-id>");
        });
        </script>

    In the URL you'll need to replace the URL with your blog url and the call-id with the id of the call.
6. Update and you're done

Mailchimp
---------

Mailchimp signup tracking (tracks every one who receives the signup configuratin mail, but can be changed to track any one who clicks on the submit button):

1. Again it's best to work in HTML mode when entering the data
2. In Mailchimp, create an embedded signup form: Lists > Choose your list > Signup embed form > choose your options, but do not disable JavaScript
3. Copy the code into a new call

    Note: by default the latest version of WordPress comes with jQuery in "no conflict" mode. This is not compatible with the Mailchimp signup form. To fix this, you need to replace every occurence of $ in the form with jQuery (capitalization is important)
4. Find the following text in the call: `function mce_success_cb(resp)` It is in the lower part of the signup code
5. A few lines lower you should see `if (resp.result=="success"){`
6. Just below this line add the tracking code:

        if (resp.result=="success") {
          ctopt_track("http://<blog-url>/<blog-path>/?ctopt_track=<call-id>");
          ...

7. Save the changes

Facebook
--------

In order to track Facebook likes, you need to use the XFBML version of the like button:

1. Get a like button here: http://developers.facebook.com/docs/reference/plugins/like/
2. When you click on "get code" copy and paste the XFBML version into a new call (again use the HTML view)
3. Below the FB code, add the tracking code:

        <script type="text/javascript">
        FB.Event.subscribe('edge.create', function(response) {
          ctopt_track("http://<blog-url>/<blog-path>/?ctopt_track=<call-id>");
        });
        </script>

4. Publish and you're done
