import { Component, OnInit} from '@angular/core';
import { Router, ActivatedRoute, ParamMap } from '@angular/router';
import { GetModulesService } from '../get-modules.service';

@Component({
  selector: 'app-module-list',
  templateUrl: './module-list.component.html',
  styleUrls: ['./module-list.component.css']
})
export class ModuleListComponent implements OnInit {

  constructor(  public allSer:GetModulesService, public actroute:ActivatedRoute, public route:Router) { }
  public loder = false;
  public moduleName:any;

  ngOnInit() {  
    this.actroute.paramMap.subscribe((params: ParamMap) => {
      this.moduleName  = params.get('modulename'); 
      this.moduleList(params.get('modulename'))
      });
  }
  public lables:any;
  public listVals:any = null;
  public contents:any = null;

  moduleList(modl){
    this.loder = true;
    this.allSer.getModuleList(modl).subscribe(
      data=>{
        this.loder = false;
        // console.log(data.html)
        // console.log(typeof data);
        // if(data ==null || data==""){
        //   this.lables = null;
        //   this.listVals = null;
           
        // }else{
        //   this.lables = data[0].list[0];
        //   this.listVals = data[0].list
        // }
        this.contents = data.html;

        setTimeout(()=>{this.navigateUser(data);},900);
        
        
      },
      err => {
        this.loder = false;
        console.log(err)
      }
    );
  }

  navigateToDetail(item){
    console.log(item)
    this.route.navigate(['/all-modules/module-detail',this.moduleName,item.record]);
  }

  navigateUser(data){
    var thisins = this;
    document.querySelectorAll('.userView').forEach( function ( item ) {
      var uitem = item;
      item.addEventListener('click', function(event) {
              console.log("item-clicked myEnt");
              console.log(item.classList);
              thisins.route.navigate(['/user']);

       }); 
    });

    document.querySelectorAll('.detailView').forEach( function ( item ) {
      var ditem = item;
      item.addEventListener('click', function(event) {
              console.log("item-clicked detail");
              console.log(ditem.classList[2]);
              let id = ditem.classList[2];
              let record = id.replace("id_", "");
              console.log(record); 
              thisins.route.navigate(['/all-modules/module-detail',thisins.moduleName,record]);

       }); 
    });

    document.querySelectorAll('.productDetailView').forEach( function ( item ) {
      var ditem = item;
      item.addEventListener('click', function(event) {
              console.log("item-clicked detail");
              console.log(ditem.classList[2]);
              let id = ditem.classList[2];
              let record = id.replace("id_", "");
              console.log(record); 
              thisins.route.navigate(['/all-modules/module-detail',"Products",record]);

       }); 
    });

    

    document.querySelectorAll('.downLoadImg').forEach( function ( item ) {
      var ditem = item;
      item.addEventListener('click', function(event) {
               console.log(this.src) ;
               var path:any = this.src;

               var link = document.createElement('a');
                link.href = path;
                link.target="_self";
                link.download = 'Download.jpg';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

       }); 
    });

    document.querySelectorAll('.faqdetailView').forEach( function ( item ) {
      var ditem = item;
      item.addEventListener('click', function(event) {
           
             
              let id = ditem.classList[2];
              let record = id.replace("id_", "");
              console.log(record); 

             let faq = data.view[2];

            let faqdetail = faq.filter(itm=>{
              return itm.id == record;
            });
            let faqdt = faqdetail[0];

            // table-responsive
            let htmls = `<div class='row'>
            <div class='col-md-4'><p><b>FAQ No</b></p> ${faqdt.faqno} </div>
            <div class='col-md-4'><p><b>Category</b></p> ${faqdt.category}</div>
            <div class='col-md-4'><p><b>Created At</b></p> ${faqdt.faqcreatedtime}</div>
            <div class='col-md-12'><br></div>
            <div class='col-md-12'><p><b>Question</b></p> ${faqdt.question}</div>
            <div class='col-md-12'><p><b>Answer</b></p> ${faqdt.answer}</div>
            </div>`;
            console.log(faqdetail[0].faqno);

            document.querySelector(".table-responsive").innerHTML = htmls;
           

       }); 
    });


    document.querySelectorAll('.category_index').forEach( function ( item ) {
      var ditem = item;
      item.addEventListener('click', function(event) {
           
             
              let id = ditem.classList[2];
              let indx = id.replace("id_", "");
             let catg =  data.view[0][parseInt(indx)];

             
             document.querySelector(".widget_titles").innerHTML =`<h5><b> Category: ${catg}</b></h5>`;
             console.log(catg)

             let faq = data.view[2];

             let htmls = '<table width="100%" border="0" cellspacing="1" cellpadding="3" class="lvt table table-striped table-bordered table-hover"><tbody>'; 
            let arr = []
             let faqdetail = faq.filter(itm=>{
              if(itm.category == catg){
                arr.push(item);
                htmls +=`<tr>
                <td>
                  <img src="assets/faq.png" valign="absmiddle">&nbsp;
                  <a class="cursor  faqdetailView id_${itm.id}">${itm.question}</a>
                </td>
                   </tr>
                   
                   <tr>
							      <td class="small">${itm.answer}</td>
			    		   	</tr>`;
              }
              return true;
            });
            if(arr.length == 0){
              htmls += "<tr><th>No Data Availble</th><td></td></tr>";
            }
            htmls += "</tbody></table>";
            let faqdt = faqdetail;
            console.log(faqdt);
 
            
            
            document.querySelector(".table-responsive").innerHTML = htmls;
            setTimeout(()=>{thisins.navigateUser(data);},900);
           

       }); 
    });


    document.querySelectorAll('.productfaqDetail').forEach( function ( item ) {
      var ditem = item;
      item.addEventListener('click', function(event) {
           
             
              let id = ditem.classList[2];
              let record = id.replace("id_", "");  

             let faq = data.view[2];

             let htmls = '<table width="100%" border="0" cellspacing="1" cellpadding="3" class="lvt table table-striped table-bordered table-hover"><tbody>'; 
            let arr =[];
             let faqdetail = faq.filter(itm=>{
              if(itm.product_id == record){
                arr.push(itm);
                htmls +=`<tr>
                <td>
                  <img src="assets/faq.png" valign="absmiddle">&nbsp;
                  <a class="cursor  faqdetailView id_${itm.id}">${itm.question}</a>
                </td>
                   </tr>
                   
                   <tr>
							      <td class="small">${itm.answer}</td>
			    		   	</tr>`;
              }
              return true;
            });

            if(arr.length == 0){
              htmls += "<tr><th>No Data Availble</th><td></td></tr>";
            }

            htmls += "</tbody></table>";
            let faqdt = faqdetail;
            console.log(faqdt);
 
            document.querySelector(".widget_titles").innerHTML =`<h5><b> Product Related</b></h5>`;
            
            document.querySelector(".table-responsive").innerHTML = htmls;
            setTimeout(()=>{thisins.navigateUser(data);},900);
           

       }); 
    });


    


  
  }

}
