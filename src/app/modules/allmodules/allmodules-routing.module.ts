import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { ModuleListComponent } from './module-list/module-list.component';
import { ModuleDetailComponent } from './module-detail/module-detail.component';


const routes: Routes = [
  {path:"module-list/:modulename", component:ModuleListComponent},
  {path:"module-detail/:modulename/:id", component:ModuleDetailComponent},
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class AllmodulesRoutingModule { }
