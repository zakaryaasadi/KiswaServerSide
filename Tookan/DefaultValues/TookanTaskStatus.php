<?php


namespace Tookan\DefaultValues;


class TookanTaskStatus{
    const Assigned = 0;
    const Started = 1;
    const Successful = 2;
    const Failed = 3;
    const InProgressOrArrived = 4;
    const Unassigned = 6;
    const AcceptedOrAcknowledged	= 7;
    const Decline = 8;
    const Cancel = 9;
    const Deleted = 10;
}