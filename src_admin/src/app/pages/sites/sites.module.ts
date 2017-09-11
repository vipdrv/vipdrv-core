import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { AppTranslationModule } from '../../app.translation.module';
import { NgaModule } from '../../theme/nga.module';

import { Ng2Bs3ModalModule } from 'ng2-bs3-modal/ng2-bs3-modal';

import { UtilsModule } from './../../utils/index';
import { SitesTableComponent } from './table/sitesTable.component';

import { ExpertsModule } from './../experts/index';
import { BeveragesModule } from './../beverages/index';

import { SiteDetailsComponent } from './details/siteDetails.component';
import { SiteOverviewComponent } from './overview/siteOverview.component';

import { SitesComponent } from './sites.component';
import { routing } from './sites.routing';

@NgModule({
    imports: [
        CommonModule,
        FormsModule,
        AppTranslationModule,
        NgaModule,
        routing,
        UtilsModule,
        Ng2Bs3ModalModule,
        ExpertsModule,
        BeveragesModule
    ],
    exports: [
        SitesComponent,
        SitesTableComponent,
        SiteDetailsComponent,
        SiteOverviewComponent
    ],
    declarations: [
        SitesComponent,
        SitesTableComponent,
        SiteDetailsComponent,
        SiteOverviewComponent
    ],
    providers: []
})
export class SitesModule {}
