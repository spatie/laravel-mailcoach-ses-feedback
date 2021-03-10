# Changelog

All notable changes to `laravel-mailcoach-ses-feedback` will be documented in this file

## 3.0.1 - 2021-03-10

- Fix an issue where campaign could be null

## 3.0.0 - 2021-03-10

- Support for Mailcoach v4

## 2.4.2 - 2021-01-15

- Fix an issue where the configuration set header was not being set when only using the mailer setting on lists

## 2.4.1 - 2021-01-15

- Fix an issue where the configuration set header was not being set when using a different mailcoach mailer

## 2.4.0 - 2020-12-15

- allow Send class to be overridden

## 2.3.5 - 2020-12-07

- Mark webhook calls as processed when no send is found

## 2.3.4 - 2020-12-03

- PHP 8 

## 2.3.3 - 2020-11-17

- Allows custom mailer with "ses" transport 

## 2.3.2 - 2020-11-06

- fix an issue where the configuration set wasn't added

## 2.3.1 - 2020-10-16

- fix compatibility with L8

## 2.3.0 - 2020-09-25

- Tag a Mailcoach v3 compatible release
- Use `external_id` for first message checking

## 2.2.3 - 2020-09-08

- add support for Laravel 8

## 2.2.2 - 2020-07-01

- Use the configured app timezone for stored feedback

## 2.2.1 - 2020-06-30

- Ignore webhook payloads that have no `eventType`
- Simplify message validation

## 2.2.0 - 2020-04-27

- fire `WebhookCallProcessedEvent` when webhook has been processed

## 2.1.2 - 2020-04-09

- fix time on feedback registration

## 2.1.1 - 2020-04-03

- fix confirming of subscriptions in AWS

## 2.1.0 - 2020-03-20

- add ability to use a custom queue connection

## 2.0.0 - 2020-03-10

- Add support for Mailcoach v2

## 1.3.1 - 2020-03-04

- Add Laravel 7 support

## 1.2.0 - 2020-02-24

- Check to make sure we haven't received the SES Webhook before - thanks @jbraband

## 1.1.0 - 2020-02-06

- Only add the SES configuration header if the message is from Mailcoach
- Make sure the SES configuration header is only set once 

## 1.0.0 - 2020-01-29

- initial release
