import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, ParamMap } from '@angular/router';
import { GetModulesService } from '../get-modules.service';

@Component({
  selector: 'app-module-list',
  templateUrl: './module-list.component.html',
  styleUrls: ['./module-list.component.css']
})
export class ModuleListComponent implements OnInit {

  constructor(public allSer:GetModulesService, public actroute:ActivatedRoute, public route:Router) { }
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

  moduleList(modl){
    this.loder = true;
    this.allSer.getModuleList(modl).subscribe(
      data=>{
        this.loder = false;
        console.log(data)
        console.log(typeof data);
        if(data ==null || data==""){
          this.lables = null;
          this.listVals = null;
           
        }else{
          this.lables = data[0].list[0];
          this.listVals = data[0].list
        }
        
        
      },
      err => console.log(err)
    );
  }

  navigateToDetail(item){
    console.log(item)
    this.route.navigate(['/all-modules/module-detail',this.moduleName,item.record]);
  }

}
