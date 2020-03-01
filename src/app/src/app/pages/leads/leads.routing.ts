import { Routes, RouterModule } from '@angular/router';
import { ModuleWithProviders } from '@angular/core';

import { LeadsComponent } from './leads.component';
// noinspection TypeScriptValidateTypes
export const routes: Routes = [
    {
        path: '',
        component: LeadsComponent,
        children: []
    }
];
export const routing: ModuleWithProviders = RouterModule.forChild(routes);