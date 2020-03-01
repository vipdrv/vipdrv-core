import { Routes, RouterModule } from '@angular/router';
import { ModuleWithProviders } from '@angular/core';

import { ExpertsComponent } from './experts.component';
// noinspection TypeScriptValidateTypes
export const routes: Routes = [
    // {
    //     path: '',
    //     component: ExpertsComponent,
    //     children: []
    // }
];
export const routing: ModuleWithProviders = RouterModule.forChild(routes);