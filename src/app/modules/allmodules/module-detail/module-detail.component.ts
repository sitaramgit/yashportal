import { Component, OnInit } from '@angular/core';
import { GetModulesService } from '../get-modules.service';
import { ActivatedRoute, Router, ParamMap } from '@angular/router';

@Component({
  selector: 'app-module-detail',
  templateUrl: './module-detail.component.html',
  styleUrls: ['./module-detail.component.css']
})
export class ModuleDetailComponent implements OnInit {

  constructor(public allSer:GetModulesService, public actroute:ActivatedRoute, public route:Router) { }
  public loder = false;
  public moduleName:any;
  public record:any;

  public lables:any;
  public listVals:any = null;
  ngOnInit() {  
    this.actroute.paramMap.subscribe((params: ParamMap) => {
      this.moduleName  = params.get('modulename');
      this.record  = params.get('id');
      this.moduleDetail(this.moduleName,this.record)
      });
  }

  moduleDetail(modul,id){
    
    this.loder = true;
    this.allSer.getModuleDetail(modul,id).subscribe(
      data=>{
        this.loder = false; 
          this.listVals = data[0][this.moduleName];
          console.log( this.listVals); 
        
        
      },
      err => console.log(err)
    );
  }

}
