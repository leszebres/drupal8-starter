
# Nomamclature OOCSS  

## Modules avec enfants  

**Classes :**

 - module : module module--variante l-layout is-state theme-theme 
 - enfant direct : module-item
 - éléments de l’enfant : item-nom

**Exemples :**  
 - list   
 - slider   
 - nav   
 - accordion
  
  
### Listes :  

    .list.list--primary  
	    .list-item  
		    a  
			    article.item-wrapper  
			        header.item-header  
			           h2.item-title  
			           p.item-subtitle  
			           p.item-tag  
      
				    div.item-media l-image
				       img/iframe…  
				       span.item-background (si img en bg)  
      
				    div.item-body  
				       p.item-desc  
         
				    footer.item-footer  
				       span.item-cta.btn--more  

  
### Navigation :  

    div/nav.nav
       ul.nav-menu  
          li.nav-item  
             p.item-title  
             p.item-subtitle   
             …  

### Slider :  

    div.slider  
       div.slider-item  
          div.item-image/item-background   
          div.item-wrapper(.row)  
             a  
	             p.item-title  
	             p.item-subtitle  
	             div.item-body  
	                p  
	             span.item-cta


  
## Modules sans enfants  
  
**Exemples :**  

 - block   
 - btn

### Blocks  

    article.block(.l-narrow)  
       .block-wrapper(.row)  
          header.block-header  
             .block-title  
             .block-subtitle  
          .block-content  
	          ...
          .block-footer  
             .block-link.btn
        
### Btn  

    .btn(.l-small)  
       .btn--content  

### Node  

    div.node  
       .node-body  
       .node-header  
       .node-footer  
       .node-date  
       .node-author  
       .node-tag  
  
### Paragraph  

    .paragraph.paragraph--type--
       .paragraph-body  
       .paragraph-media  
       …

## Ordre des déclarations CSS :



        /* position, boite, taille */  
        position: absolute;  
        z-index: 5;  
        top: 0;  
        bottom: auto;  
        left: 0;  
        right: auto;  
        transform: scale(1);  
        display: block;  
        clear: both;  
        float: left;  
        width: 100%;  
        height: auto;  
        margin: 0;  
        padding: 0;  
        border: 0;  
        border-radius: 10px;  
        
        /* Texte */  
        color: #FFF;  
        font-family: Arial, Helvetica, sans-serif;  
        font-size: 1em;  
        font-weight: bold;  
        font-style: italic;  
        line-height: 1.1;  
        text-align: center;  
        text-transform: uppercase;  
        text-decoration: none;  
        text-shadow: none;  
        white-space: nowrap;  
        
        /* Habillage */  
        background: #000;  
        
        /* Autres */  
        opacity: 1;  
        overflow: hidden;  
        box-shadow: none;  
        transition: .2s;  
        box-sizing: border-box;

