<?php

/**
 * Laravel/Fortify's own 3-key auth-failure namespace — used directly by
 * FortifyServiceProvider::configureActions() (the ValidationException thrown
 * for a frozen/suspended account uses Fortify::username() as the field, not
 * this file, but Fortify's own login attempt failure and its login-throttle
 * lockout message both resolve through here). Never published in this app
 * before, so these always rendered in English — same root cause as
 * validation.php, see that file's comment.
 */
return [
    'failed' => 'Àlàyé wọ̀nyí kò bá àkọsílẹ̀ wa mu.',
    'password' => 'Ọ̀rọ̀ ìpamọ́ tí a fi sílẹ̀ kò tọ́.',
    'throttle' => 'O ti gbìyànjú láti wọlé ní ọ̀pọ̀ ìgbà jù. Jọ̀wọ́ tún gbìyànjú lẹ́yìn ìṣẹ́jú-àáyá :seconds.',
];
