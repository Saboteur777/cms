<?php
/**
 * @link      https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license   https://craftcms.com/license
 */

namespace craft\helpers;

use craft\errors\MissingComponentException;
use craft\events\RegisterComponentTypesEvent;
use craft\mail\Mailer;
use craft\mail\Message;
use craft\mail\transportadapters\BaseTransportAdapter;
use craft\mail\transportadapters\Gmail;
use craft\mail\transportadapters\Php;
use craft\mail\transportadapters\Sendmail;
use craft\mail\transportadapters\Smtp;
use craft\mail\transportadapters\TransportAdapterInterface;
use craft\models\MailSettings;
use yii\base\Event;

/**
 * Class DateTimeHelper
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since  3.0
 */
class MailerHelper
{
    // Constants
    // =========================================================================

    /**
     * @event RegisterComponentTypesEvent The event that is triggered when registering mailer transport adapter types.
     */
    const EVENT_REGISTER_MAILER_TRANSPORT_TYPES = 'registerMailerTransportTypes';

    // Static
    // =========================================================================

    /**
     * Returns all available mailer transport adapter classes.
     *
     * @return string[]
     */
    public static function allMailerTransportTypes()
    {
        $transportTypes = [
            Php::class,
            Sendmail::class,
            Smtp::class,
            Gmail::class,
        ];

        $event = new RegisterComponentTypesEvent([
            'types' => $transportTypes
        ]);
        Event::trigger(static::class, self::EVENT_REGISTER_MAILER_TRANSPORT_TYPES, $event);

        return $event->types;
    }

    /**
     * Creates a transport adapter based on the given mail settings.
     *
     * @param string     $type
     * @param array|null $settings
     *
     * @return TransportAdapterInterface
     * @throws MissingComponentException if $type is missing
     */
    public static function createTransportAdapter(string $type, array $settings = null)
    {
        /** @var BaseTransportAdapter $adapter */
        $adapter = Component::createComponent([
            'type' => $type,
            'settings' => $settings,
        ], TransportAdapterInterface::class);

        return $adapter;
    }

    /**
     * Creates a mailer component based on the given mail settings.
     *
     * @param MailSettings $settings
     *
     * @return Mailer
     */
    public static function createMailer(MailSettings $settings)
    {
        try {
            $adapter = self::createTransportAdapter($settings->transportType, $settings->transportSettings);
        } catch (MissingComponentException $e) {
            // Fallback to the PHP mailer
            $adapter = new Php();
        }

        $mailer = new Mailer([
            'messageClass' => Message::class,
            'from' => [$settings->fromEmail => $settings->fromName],
            'template' => $settings->template,
            'transport' => $adapter->getTransportConfig(),
        ]);

        return $mailer;
    }
}
