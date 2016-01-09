<?php
/*
Name: Twitter Box
Author: Pedro Lima - pedrolima.io
Description: Take control of twitter feed.
Version: 1.0
Class: twitter_box
License: MIT
*/

class twitter_box extends thesis_box {
//    public $type = 'rotator';
    // translation functions are added here
    protected function translate()
    {
        $this->title = $this->name = __('Twitter Box', $this->_class);
    }

    public function preload()
    {
        global $thesis; // this gives us access to thesis version number

        // register and enqueue stylesheet
        wp_register_style('twitter-box-css', THESIS_USER_BOXES_URL. '/twitter-box/css/box.css', false, $thesis->version);
        wp_enqueue_style('twitter-box-css');

        // register and enqueue JavaScript
        $dependents = array('jquery'); // array of dependents
        $to_footer = true;
        wp_register_script( 'twitter-box-js', THESIS_USER_BOXES_URL. '/twitter-box/js/twitter.js', $dependents, $thesis->version, $to_footer);
        wp_enqueue_script( 'twitter-box-js' );

        wp_register_script( 'twitter-widget', '//platform.twitter.com/widgets.js' );
        wp_enqueue_script( 'twitter-widget' );



    }

    // HTML Options
    public function html_options()
    {
        global $thesis;
        $html = $thesis->api->html_options(array(
            'p' => 'p',
            'div' => 'div',
            'span' => 'span'), 'p');
        unset($html['id']);
        return $html;
    }
    // Instance based options
    protected function options() {
        return array(
            'consumer_key' => array(
                'type' => 'text',
                'label' => __('Consumer Key', 'twitter_box_namespace'),
                'tooltip' => __('Enter some sample text for our sample Box.', 'twitter_box_namespace')
            ),
            'consumer_secret' => array(
                'type' => 'text',
                'label' => __('Consumer Secret', 'my_box_namespace'),
                'tooltip' => __('Enter some sample text for our sample Box.', 'twitter_box_namespace')
            ),
            'oauth_token' => array(
                'type' => 'text',
                'label' => __('Access token', 'my_box_namespace'),
                'tooltip' => __('Enter some sample text for our sample Box.', 'twitter_box_namespace')
            ),
            'oauth_secret' => array(
                'type' => 'text',
                'label' => __('Access token secret', 'my_box_namespace'),
                'tooltip' => __('Enter some sample text for our sample Box.', 'twitter_box_namespace')
            ),
            'username' => array(
                'type' => 'text',
                'label' => __('Username', 'my_box_namespace'),
                'tooltip' => __('Enter some sample text for our sample Box.', 'twitter_box_namespace')
            ),
            'tweet_count' => array(
                'type' => 'select',
                'options' => array(
                    '1' => __('1 Tweet', 'twitter_box_namespace'),
                    '2' => __('2 Tweet', 'twitter_box_namespace'),
                    '3' => __('3 Tweet', 'twitter_box_namespace'),
                    '4' => __('4 Tweet', 'twitter_box_namespace')
                )
            )
        );
    }

    // class based options
    public function class_options()
    {
        global $thesis;
        return array(
            'consumer_key' => array(
                'type' => 'text',
                'label' => __('Consumer Key', 'twitter_box_namespace'),
                'tooltip' => __('Enter some sample text for our sample Box.', 'twitter_box_namespace')
            ),
            'consumer_secret' => array(
                'type' => 'text',
                'label' => __('Consumer Secret', 'my_box_namespace'),
                'tooltip' => __('Enter some sample text for our sample Box.', 'twitter_box_namespace')
            ),
            'oauth_token' => array(
                'type' => 'text',
                'label' => __('Access token', 'my_box_namespace'),
                'tooltip' => __('Enter some sample text for our sample Box.', 'twitter_box_namespace')
            ),
            'oauth_secret' => array(
                'type' => 'text',
                'label' => __('Access token secret', 'my_box_namespace'),
                'tooltip' => __('Enter some sample text for our sample Box.', 'twitter_box_namespace')
            ),
            'username' => array(
                'type' => 'text',
                'label' => __('Username', 'my_box_namespace'),
                'tooltip' => __('Enter some sample text for our sample Box.', 'twitter_box_namespace')
            ),
            'tweet_count' => array(
                'type' => 'select',
                'options' => array(
                    '1' => __('1 Tweet', 'twitter_box_namespace'),
                    '2' => __('2 Tweet', 'twitter_box_namespace'),
                    '3' => __('3 Tweet', 'twitter_box_namespace'),
                    '4' => __('4 Tweet', 'twitter_box_namespace')
                )
            )
        );
    }
    protected function design() {
        return array(
            'colors' => $this->color_scheme(/* color scheme array */));
    }
    // the html method is where our output happen
    public function html($args = array())
    {
        global $thesis;
        extract($args = is_array($args) ? $args : array());

        // this method combines all of our options into a single array
        // instance based options take precedence over class based options.
        $options = $thesis->api->get_options(array_merge($this->_

            , $this->_options()), $this->options);
//        $html = !empty($options['html']) ? $options['html'] : 'p';

        // We have now added our post meta option to our $string variable
        // Since the post meta is checked first, it will output if it exists else it will continue
        $message = array( (!empty($this->options['consumer_key']) ?
            $this->options['consumer_key'] : (!empty($options['consumer_key']) ? $options['consumer_key'] : '')),
            (!empty($this->options['consumer_secret']) ?
                $this->options['consumer_secret'] : (!empty($options['consumer_secret']) ? $options['consumer_secret'] : '')),
            (!empty($this->options['oauth_token']) ?
                $this->options['oauth_token'] : (!empty($options['oauth_token']) ? $options['oauth_token'] : '')),
            (!empty($this->options['oauth_secret']) ?
                $this->options['oauth_secret'] : (!empty($options['oauth_secret']) ? $options['oauth_secret'] : '')),
            (!empty($this->options['username']) ?
                $this->options['username'] : (!empty($options['username']) ? $options['username'] : '')),
            (!empty($this->options['tweet_count']) ?
                $this->options['tweet_count'] : (!empty($options['tweet_count']) ? $options['tweet_count'] : '')),
        );

        $GLOBALS = $message;

        include_once('include/twitter.php');

        //-------------------------------------------------------------- Timeline HTML output

        echo '<ul id="tweet-list" class="tweet-list">';
# The tweets loop
        foreach ($twitter_data as $tweet) {
            $retweet = $tweet['retweeted_status'];
            $isRetweet = !empty($retweet);
# Retweet - get the retweeter's name and screen name
            $retweetingUser = $isRetweet ? $tweet['user']['name'] : null;
            $retweetingUserScreenName = $isRetweet ? $tweet['user']['screen_name'] : null;
# Tweet source user (could be a retweeted user and not the owner of the timeline)
            $user = !$isRetweet ? $tweet['user'] : $retweet['user'];
            $userName = $user['name'];
            $userScreenName = $user['screen_name'];
            $userAvatarURL = stripcslashes($user['profile_image_url']);
            $userAccountURL = 'http://twitter.com/' . $userScreenName;
# The tweet
            $id = $tweet['id'];
            $formattedTweet = !$isRetweet ? formatTweet($tweet['text']) : formatTweet($retweet['text']);
            $statusURL = 'http://twitter.com/' . $userScreenName . '/status/' . $id;
            $date = timeAgo($tweet['created_at']);
# Reply
            $replyID = $tweet['in_reply_to_status_id'];
            $isReply = !empty($replyID);
# Tweet actions (uses web intents)
            $replyURL = 'https://twitter.com/intent/tweet?in_reply_to=' . $id;
            $retweetURL = 'https://twitter.com/intent/retweet?tweet_id=' . $id;
            $favoriteURL = 'https://twitter.com/intent/favorite?tweet_id=' . $id;
            ?>
            <li id="<?php echo 'tweetid-' . $id; ?>" class="tweet<?php
            if ($isRetweet) echo ' is-retweet';
            if ($isReply) echo ' is-reply';
            if ($tweet['retweeted']) echo ' visitor-retweeted';
            if ($tweet['favorited']) echo ' visitor-favorited'; ?>">
                <div class="tweet-info">
                    <div class="user-info">
                        <a class="user-avatar-link" href="<?php echo $userAccountURL; ?>">
                            <img class="user-avatar" src="<?php echo $userAvatarURL; ?>">
                        </a>
                        <p class="user-account">
                            <a class="user-name" href="<?php echo $userAccountURL; ?>"><strong><?php echo $userName; ?></strong></a>
                            <a class="user-screenName" href="<?php echo $userAccountURL; ?>">@<?php echo $userScreenName; ?></a>
                        </p>
                    </div>
                    <a class="tweet-date permalink-status" href="<?php echo $statusURL; ?>" target="_blank">
                        <?php echo $date; ?>
                    </a>
                </div>
                <blockquote class="tweet-text">
                    <?php
                    echo '<p>' . $formattedTweet . '</p>';
                    echo '<p class="tweet-details">';
                    if ($isReply) {
                        echo '
<a class="link-reply-to permalink-status" href="http://twitter.com/' . $tweet['in_reply_to_screen_name'] . '/status/' . $replyID . '">
In reply to...
</a>
';
                    }
                    if ($isRetweet) {
                        echo '
<span class="retweeter">
Retweeted by <a class="link-retweeter" href="http://twitter.com/' . $retweetingUserScreenName . '">' .
                            $retweetingUser
                            . '</a>
</span>
';
                    }
                    echo '<a class="link-details permalink-status" href="' . $statusURL . '" target="_blank">Details</a></p>';
                    ?>
                </blockquote>
                <div class="tweet-actions">
                    <a class="action-reply" href="<?php echo $replyURL; ?>">Reply</a>
                    <a class="action-retweet" href="<?php echo $retweetURL; ?>">Retweet</a>
                    <a class="action-favorite" href="<?php echo $favoriteURL; ?>">Favorite</a>
                </div>
            </li>
        <?php
        } # End tweets loop
# Close the timeline list
        echo '</ul>';

    }

}