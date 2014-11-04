---
layout: post
title: Hardware Details
author: Matt Amos
---

Details of the hardware.

## Secondary database server

The defining characteristic of a database server, for a database which
doesn't fit entirely within RAM, is I/O performance. For that reason,
we choose a chassis which is able to accomodate a very large number of
disks --- in this case, a whopping 72 2.5" 10K disks. Although we
don't need all 72 (yet) we do need 25 900GB disks and it's good to
know that the capacity is there for future expansion.

To keep as much of the data accessible as quickly as possible, we also
want lots of RAM to use as system cache. 256GB seems to be the largest
we can reasonably get before it starts getting _really_ expensive.

Finally, we add on a couple of good quality, enterprise grade SSDs. In
the last few years there have been several really good contenders in
the consumer (or pro-sumer) market but, for reliability and especially
under hard power-off conditions, you still can't beat enterprise
grade.

Approximate cost: £ 21k

<!-- S53959 = £ 20,440 -->
<!-- alternatively, S53963 = £ 14,207 -->

## Tile rendering servers

#### Primary

Tile serving is about really good disk _read_ performance, lots of
memory and a good CPU. It's possible to fit almost all of a rendering
database in memory, if you buy enough. The optimal spot at the moment
seems to be getting about 128GB RAM and a couple of pro-sumer SSDs for
the most often accessed disk data. The amount of data being read
requires a good RAID controller, too. In this case, we're looking at 8
600GB 15k SAS disks with a couple of 512GB Samsung 840 Pro SSDs.

Approximate cost: £ 9.5k

<!-- S53960 = £ 9,365-->

#### Secondary

It's all about the I/O --- the secondary tile server can't handle it
at the moment, but the addition of a good RAID controller and a pair
of 512GB Samsung 840 Pro SSDs will soon sort that out.

Approximate cost: £ 1.5k

<!-- LSI 9361-8i kit = £520 -->
<!-- 2x Samsung 850 Pro? 2x£470-->

## Imagery server

Serving aerial imagery is also about good disk performance, same as
serving tiles but the aerial imagery tiles tend to be quite large and
not rendered as often. Reprojecting and slicing up aerial imagery can
be CPU intensive though, so the specification for an aerial
imagery server looks a lot like that of a tile server but with more,
slower disk --- 128GB RAM, a couple of low-voltage E5-2650Ls and 6 4TB
SAS3 disks.

Approximate cost: £ 9k

<!-- S53961 = £ 8,740-->

## Services machine replica

The services machine does a lot of odd jobs; it serves GPS traces to
the website machines, it serves the
[planet](http://planet.openstreetmap.org), stores backups, aggregates
log files, runs the Chef configuration server and serves internal DNS
requests. All of this means it needs to be a general-purpose
machine, with quite a lot of RAM, decent CPUs and a lot of space for
storing all those GPS traces, dumps and planets. A suitable spec is a
couple of decent CPUs (E5-2650LV2s) with 256GB RAM and 9 4TB SAS2
disks.

Approximate cost: £ 10.5k

<!-- S53962 = £ 10,220-->

## Consumables

Parts on hardware fail with alarming regularity, especially the
mechanical parts. Usually these are covered by the standard warranty
we get on all new machines, but some machines aren't covered ---
either because they're donated or we buy them out of warranty to get a
good deal. The failures themselves are random, and therefore pretty
hard to predict, but a good rule of thumb is about 10% of the overall
budget as a contingency, so that's about what we've estimated here.

Approximate cost: £ 4.5k

# Total

Approximately £56k.
