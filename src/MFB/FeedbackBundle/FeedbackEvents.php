<?php
namespace MFB\FeedbackBundle;
final class FeedbackEvents
{
    const REGULAR_INITIALIZE = 'feedback.regular.initialize';
    const REGULAR_COMPLETE = 'feedback.regular.complete';
    const INVITE_INITIALIZE = 'feedback.invite.initialize';
    const INVITE_COMPLETE = 'feedback.invite.complete';
    const INVITE_SEND_COMPLETE = 'feedback.invite.send.complete';
    const INVITE_SEND_INITIALIZE = 'feedback.invite.send.initialize';
}