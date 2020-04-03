# Changelog

All notable changes to `laravel-mailcoach-ses-feedback` will be documented in this file

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
