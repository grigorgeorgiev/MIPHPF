# MIPHPF Makefile
# Copyright (c) 2007-2008 Mirchev Ideas Ltd. All rights reserved.

DISTDIR=/tmp/miphpf-dist
DISTFILES=doc/ examples include tests Makefile ChangeLog.txt
CURRENTDATE=`date +%Y%m%d`
DISTRIB=miphpf-$(CURRENTDATE).tgz

$(DISTRIB): doc
	mkdir -p $(DISTDIR)/miphpf
	list='$(DISTFILES)'; for file in $$list; do \
		cp -pR $$file -t $(DISTDIR)/miphpf/; \
	done
	tar zcf $(DISTRIB) --exclude=CVS -C $(DISTDIR) miphpf
	rm -rf $(DISTDIR)
	
doc:
	phpdoc --title "MIPHPF Documentation" --sourcecode "on" -d include/miphpf -t doc

.PHONY: doc
