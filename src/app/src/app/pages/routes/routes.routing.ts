import { Routes, RouterModule } from '@angular/router';
import { ModuleWithProviders } from '@angular/core';

import { RoutesComponent } from './routes.component';
// noinspection TypeScriptValidateTypes
export const routes: Routes = [
    // {
    //     path: '',
    //     component: RoutesComponent,
    //     children: []
    // }
];
export const routing: ModuleWithProviders = RouterModule.forChild(routes);