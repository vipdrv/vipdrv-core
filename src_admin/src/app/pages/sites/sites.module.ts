import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { AppTranslationModule } from '../../app.translation.module';
import { NgaModule } from '../../theme/nga.module';

import { UtilsModule } from './../../utils/index';
import { SitesTableComponent } from './table/sitesTable.component';

import { SitesComponent } from './sites.component';
import { routing } from './sites.routing';
@NgModule({
    imports: [
        CommonModule,
        FormsModule,
        AppTranslationModule,
        NgaModule,
        routing,
        UtilsModule
    ],
    declarations: [
        SitesComponent,
        SitesTableComponent
    ],
    providers: []
})
export class SitesModule {}
