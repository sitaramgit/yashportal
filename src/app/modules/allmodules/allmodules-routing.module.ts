import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { ModuleListComponent } from './module-list/module-list.component';
import { ModuleDetailComponent } from './module-detail/module-detail.component';
import { TicketCreateComponent } from './ticket-create/ticket-create.component';


const routes: Routes = [
  {path:"module-list/:modulename", component:ModuleListComponent},
  {path:"module-detail/:modulename/:id", component:ModuleDetailComponent},
  {path:"ticket-create", component:TicketCreateComponent},
  
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class AllmodulesRoutingModule { }
