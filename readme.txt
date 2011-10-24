=== AdHerder ===

Contributors: pbackx
Donate link: http://grasshopperherder.com/
Tags: plugin, widget, automatic, ad, manage
Requires at least: 3.0
Tested up to: 3.2.1
Stable tag: 1.0

AdHerder is the ultimate in automated advertisement management. Enter your 
ads and the plugin will track the number of impressions and conversions. 
Based on this data, the plugin selects the best ad for each individual user.

== Description ==

AdHerder is the ultimate in automated advertisement management. Enter your 
ads and the plugin will track the number of impressions and conversions. 
Based on this data, the plugin selects the best ad for each individual user.

The plugin will automatically instrument and log clicks on links. Please
see the FAQ section for information on how to track Facebook likes, Twitter
follows and Mailchimp signups.

== Installation ==

Install through the admin interface, or manually in wp-content/plugins/adherder

Once the plugin is enabled, a new post type will be available, called "Ad".
Create at least one ad and add the AdHerder widget to your theme.

== Frequently Asked Questions ==

= How does AdHerder select which ad to display? =

AdHerder tracks the user behavior through a cookie. Based on this data the
ad is selected as follows: An ad that has not been seen by the user has the
highest priority. An ad on which the user has already converted (clicked)
has the lowest priority. You can tweak this behavior in the settings screen.

= How does AdHerder track conversions? =

The plugin automatically tracks clicks on links with a small piece of JavaScript.
If you want to monitor different types of conversion, you need to manually
call the `adherder_track_conversion()` function with as argument the ID of the ad.

= Where do I find the Ad ID? =

To find the ID of the call you can open the reports page and check the table. 
The first column shows the id. Another option is to edit the call and look 
at the url: `/wp-admin/post.php?post=7&action=edit`. In this case, the id is 7.

= How do I track Twitter follows? =

Tracking Twitter conversions (this only tracks people who click on follow and weren't already following you):

1. Start by creating your follow button at http://twitter.com/about/resources/followbutton
2. Create a new call to action and switch the editor to HTML mode
3. Paste the Twitter follow button code from step 1
4. Save the call
5. Below this, add the following:

        <script type="text/javascript">
        twttr.events.bind('follow', function(event) {
          adherder_track_conversion(<call-id>);
        });
        </script>

    In the URL you'll need to replace the URL with your blog url and the call-id with the id of the call.
6. Update and you're done

= How do I track Mailchimp signups? =

Mailchimp signup tracking (tracks every one who receives the signup configuratin mail, but can be changed to track any one who clicks on the submit button):

1. Again it's best to work in HTML mode when entering the data
2. In Mailchimp, create an embedded signup form: Lists > Choose your list > Signup embed form > choose your options, but do not disable JavaScript
3. Copy the code into a new call

    Note: by default the latest version of WordPress comes with jQuery in "no conflict" mode. This is not compatible with the Mailchimp signup form. To fix this, you need to replace every occurence of $ in the form with jQuery (capitalization is important)
4. Find the following text in the call: `function mce_success_cb(resp)` It is in the lower part of the signup code
5. A few lines lower you should see `if (resp.result=="success"){`
6. Just below this line add the tracking code:

        if (resp.result=="success") {
          adherder_track_conversion(<call-id>);
          ...

7. Save the changes

= How do I track Facebook likes? =

In order to track Facebook likes, you need to use the XFBML version of the like button:

1. Get a like button here: http://developers.facebook.com/docs/reference/plugins/like/
2. When you click on "get code" copy and paste the XFBML version into a new call (again use the HTML view)
3. Below the FB code, add the tracking code:

        <script type="text/javascript">
        FB.Event.subscribe('edge.create', function(response) {
          adherder_track_conversion(<call-id>);
        });
        </script>

4. Publish and you're done

= Can I force the plugin to display a certain ad? For testing? =

Yes you can. It is possible to override the automatic selection of ads. 
Add a `ctopt_ad` parameter to the request. For instance, show the add with id 10:

    http://grasshopperherder.com/?adherder_ad=10


== Changelog ==

= Version 1.0 =

* Initial version.
