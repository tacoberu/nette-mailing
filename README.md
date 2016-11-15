Nette Mailer
============

Mailer support for Nette. Support logging mail to files.

Inspired by Pavel Janda (https://componette.com/ublaboo/mailing/). Many thanks.

Starts:

    composer require tacoberu/nette-mailing

Register extension in config file:

    extensions:
        mailing: Taco\Nette\Mailing\Extension

    mailing:
        do: log
        sender: foo@bar.baz
        log_directory: %appDir%/../var/log/mails

Using service Taco\Nette\Mailing\MailingService

    $this->container->getByType(Taco\Nette\Mailing\MailingService::class)->send('contact', 'recipient@example.com', ['greating' => 'Salut...']);
