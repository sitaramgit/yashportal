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

        setTimeout(()=>{this.navigateUser();},900)
        
        
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

  navigateUser(){
    var thisins = this;
    document.querySelectorAll('.userView').forEach( function ( item ) {
      var uitem = item;
      item.addEventListener('click', function(event) {
              console.log("item-clicked myEnt");
              console.log(item.classList);

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
    console.log(90);
  }

}
