Humanize PHP
============
_A quick port of the useful aspects of [Django's Humanize library](http://docs.djangoproject.com/en/1.3/ref/contrib/humanize/)._ 

Installation
------------
This package is available in Packagist/Composer as ``gburtini/humanize-php``.

Features
--------

* ``apnumber($n)`` returns the "Associated Press style" number, ``$n``, where numbers 1 thru 9 are returned as a word.
* ``intcomma($n)`` comma-separates an integer, unlike Django, it does not respect format localization.
* ``intword($n)`` returns the "word" for a given large number, for example, 1000000 becomes 1.0 million.
* ``naturalday($timestamp, $format)`` returns "today", "yesterday" or "tomorrow" from a given ``$timestamp``, alternatively returns the date in ``$format``.
* ``ordinal($n)`` converts an integer in to its ordinal string (1st, 2nd, ...)
* ``checkize($n)`` converts a number in to its check (cheque) ready word form. 65535 becomes "sixty five thousand, five hundred and thirty five." *not in Django*

Future Work
-----------
The library is currently missing ``naturaltime``, which should take in a timestamp and return "now" or "5 minutes ago" type strings. 

License
-------
Copyright (C) 2011-2015 Giuseppe Burtini 

Licensed under the Apache License, Version 2.0 (the "License"); you may not use this library except in compliance with the License. You may obtain a copy of the License at

	http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.

_Django is a registered trademark of the Django Software Foundation. The Humanize-PHP project has no association with the Django Software Foundation._
