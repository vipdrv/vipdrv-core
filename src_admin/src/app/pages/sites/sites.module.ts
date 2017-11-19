import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { AppTranslationModule } from '../../app.translation.module';
import { NgaModule } from '../../theme/nga.module';
import { Ng2Bs3ModalModule } from 'ng2-bs3-modal/ng2-bs3-modal';
import { PaginationModule } from 'ng2-bootstrap';
import { BusyModule } from 'angular2-busy';
import { UtilsModule } from './../../utils/index';
import { SitesTableComponent } from './table/sitesTable.component';
import { BeveragesModule } from './../beverages/index';
import { ExpertsModule } from './../experts/index';
import { RoutesModule } from './../routes/index';
import { WidgetPreviewModule } from './../widgetPreview/index';
import { SiteDetailsEditComponent } from './details/edit/siteDetailsEdit.component';
import { SiteDetailsRelationsComponent } from './details/relations/siteDetailsRelations.component';
import { SiteOverviewComponent } from './overview/siteOverview.component';
import { SiteContactsComponent } from './contacts/siteContacts.component';
import { SitesComponent } from './sites.component';
import { routing } from './sites.routing';

import { SiteCardsComponent } from './cards/siteCards.component';
import { SiteCardComponent } from './cards/siteCard.component';
@NgModule({
    imports: [
        CommonModule,
        FormsModule,
        AppTranslationModule,
        NgaModule,
        routing,
        UtilsModule,
        Ng2Bs3ModalModule,
        PaginationModule,
        BusyModule,
        BeveragesModule,
        ExpertsModule,
        RoutesModule,
        WidgetPreviewModule
    ],
    exports: [
        SitesComponent,
        SitesTableComponent,
        SiteCardsComponent,
        SiteCardComponent,
        SiteDetailsEditComponent,
        SiteDetailsRelationsComponent,
        SiteOverviewComponent,
        SiteContactsComponent
    ],
    declarations: [
        SitesComponent,
        SitesTableComponent,
        SiteCardsComponent,
        SiteCardComponent,
        SiteDetailsEditComponent,
        SiteDetailsRelationsComponent,
        SiteOverviewComponent,
        SiteContactsComponent
    ],
    providers: []
})
export class SitesModule {}


