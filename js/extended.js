/*
 *  Custom JavaScript functions - EXTENDED
 */

$(document).ready(function() {
  $('div.page-content').css({ 'background-color': '#f9fcff' });
  $('a.logo img.img-logo').fadeOut(function() {
    $(this).fadeIn();
  });
});

function Pager(tableName, itemsPerPage) {
	
	this.tableName = tableName;
	this.itemsPerPage = itemsPerPage;
	this.currentPage = 0;
	this.pages = 0;
	this.inited = false;
    
	this.showRecords = function(rowFrom, rowTo) {
		var rows = document.getElementById(tableName).rows;
		for (var i = 0; i < rows.length; i++) {
		if (i >= rowFrom && i < rowTo)
			rows[i].style.display = '';
		else
			rows[i].style.display = 'none';
		}
	}
    
	this.showPage = function(pageNumber) {
		if (!this.inited) return;

		var oldPageAnchor = document.getElementById('pg' + (this.currentPage + 1));
		oldPageAnchor.className = 'pg-normal';

		this.currentPage = pageNumber;
		var newPageAnchor = document.getElementById('pg' + (this.currentPage + 1));
		newPageAnchor.className = 'pg-selected';

		var rowFrom = pageNumber * itemsPerPage;
		var rowTo = rowFrom + itemsPerPage;

		this.showRecords(rowFrom, rowTo);
	}   
    
	this.prev = function() {
		if (this.currentPage > 0)
			this.showPage(this.currentPage - 1);
	}
    
	this.next = function() {
		if (this.currentPage < this.pages - 1)
			this.showPage(this.currentPage + 1);
	}                        
    
	this.init = function() {
		var rows = document.getElementById(tableName).rows;
		var records = rows.length; 
		this.pages = Math.ceil(records / itemsPerPage);
		this.inited = true;
	}

	this.showPageNav = function(pagerName, positionId) {
		if (!this.inited) return;
		
		var element = document.getElementById(positionId);

		var pagerHtml = '<span onclick="' + pagerName + '.prev();" class="pg-normal"> &#171 Prev </span> | ';
		for (var page = 1; page <= this.pages; page++) {
			pagerHtml += '<span id="pg' + page + '" class="pg-normal" onclick="' + pagerName + '.showPage(' + (page - 1) + ');">' + page + '</span> | ';
		}
		pagerHtml += '<span onclick="' + pagerName+'.next();" class="pg-normal"> Next &#187;</span>';            

		element.innerHTML = pagerHtml;
	}
}
