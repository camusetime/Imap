<?php

    /** @var $message \Fetch\Message */
    foreach ($messages as $message) {
        echo "Subject: {$message->getSubject()}"."<br>";
    }

    /*echo "<pre>";
    print_r($messages);
    echo "</pre>";*/
