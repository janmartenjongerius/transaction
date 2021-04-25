# Introduction

The library `johmanx10/transaction` allows splitting up scripted operations into
bite sized chunks and execute them in an atomic<sup>1</sup> transaction.

Operations are staged before the transaction is executed. This allows the
transaction to verify if its operations are likely to succeed when invoked.

Operations can also be rolled back. This is done in reverse order of execution.

<small><sup>1</sup> Resistance against power failures is not covered by this
library and is left up to its implementers.</small>

# Installation

```
composer require johmanx10/transaction
```

# Features

- Perform operations in a transaction
- Stage operations
- Rollback transaction / operations
- Dry-run transactions
- Granular control over transaction behavior
   - Compatible with [PSR-14 event dispatcher](https://www.php-fig.org/psr/psr-14/)

> **!! Needs links to feature documentation !!**

# Changes since version 1

[Design goals for version 2](https://github.com/johmanx10/transaction/milestone/3)
have made for significant changes between the major versions.

> - Give userspace more agency (I.e.: do not roll back automatically, to help debugging)
> - Simplify documentation and examples (By reducing complexity of the implementations of calling code)
> - Split up responsibilities of calling code (Caller A only commits, rolling back can be forwarded to caller B)
> - Allow dry-runs by implementing staging functionality
> - Allow userspace to listen for invocation, rollback and staging of operations
> 
> A focus will be made to use PHP 8 features in the new major version.

> **!! Needs link to upgrade guides !!**

# Badges of honor

<table>
  <tbody>
    <tr>
      <th>Versions</th>
      <td>

[![Packagist](https://img.shields.io/packagist/v/johmanx10/transaction.png)](https://packagist.org/packages/johmanx10/transaction)
![PHP from Packagist](https://img.shields.io/packagist/php-v/johmanx10/transaction.svg)
      </td>
    </tr>
    <tr>
      <th>Usage</th>
      <td>

[![Packagist](https://img.shields.io/packagist/dt/johmanx10/transaction.png)](https://packagist.org/packages/johmanx10/transaction/stats)
[![Packagist](https://img.shields.io/packagist/dm/johmanx10/transaction.png)](https://packagist.org/packages/johmanx10/transaction/stats)
      </td>
    </tr>
    <tr>
      <th>Quality</th>
      <td>

[![Build Status](https://scrutinizer-ci.com/g/johmanx10/transaction/badges/build.png?b=master)](https://scrutinizer-ci.com/g/johmanx10/transaction/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/johmanx10/transaction/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/johmanx10/transaction/?branch=master)        
[![Code Coverage](https://scrutinizer-ci.com/g/johmanx10/transaction/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/johmanx10/transaction/?branch=master)
      </td>
    </tr>
    <tr>
      <th>Legal</th>
      <td>

[![Packagist](https://img.shields.io/packagist/l/johmanx10/transaction.svg)](LICENSE)
      </td>
    </tr>
  </tbody>
</table>
