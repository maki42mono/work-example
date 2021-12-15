<?php

class VoteServiceInstanceHelper
{
    public static function getVoteCandidateServiceInstance(): VoteCandidateService
    {
        session_start();
        $candidates_session = $_SESSION['candidates'];
        $now = date('Y-m-d H:i:s');
        if (!$candidates_session || $candidates_session['expire'] < $now || count(
                $_SESSION['candidates']['service']->getVoteCandidates()
            ) < 1) {
            $service = (new VoteCandidateService(566))
                ->buildVoteCandidates();
            $_SESSION['candidates']['expire'] = date(
                'Y-m-d H:i:s',
                strtotime(sprintf('+%d seconds', VoteCandidateService::CACHED_TIME + 10))
            );
            $_SESSION['candidates']['service'] = $service;
            $_SESSION['candidates']['hashes'] = $service->getCandidatesHashes();
        } else {
            /** @var VoteCandidateService $service */
            $service = $_SESSION['candidates']['service'];
            $service->setCandidatesHashes($_SESSION['candidates']['hashes']);
        }

        return $service;
    }

    public static function updateService(VoteCandidateService $VoteCandidateService)
    {
        $_SESSION['candidates']['service'] = $VoteCandidateService;
    }
}