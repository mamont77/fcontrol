SELECT flightRefuelForm.id AS refuelId, flightRefuelForm.headerId AS refuelHeaderId, flightRefuelForm.agentId AS refuelAgentId, flightRefuelForm.legId AS refuelLegId, flightRefuelForm.airportId AS refuelAirportId, flightRefuelForm.quantityLtr AS refuelQuantityLtr, flightRefuelForm.quantityOtherUnits AS refuelQuantityOtherUnits, flightRefuelForm.unitId AS refuelUnitId, flightRefuelForm.priceUsd AS refuelPriceUsd, flightRefuelForm.totalPriceUsd AS refuelTotalPriceUsd, flightRefuelForm.date AS refuelDate, flightRefuelForm.status AS refuelStatus, flight.parentId AS flightParentId, flight.refNumberOrder AS flightRefNumberOrder, flight.dateOrder AS flightDateOrder, flight.kontragent AS flightAgentId, flight.airOperator AS flightAirOperator, flight.aircraftId AS flightAircraftId, flight.alternativeAircraftId1 AS flightAlternativeAircraftId1, flight.alternativeAircraftId2 AS flightAlternativeAircraftId2, flight.status AS flightStatus, refuelAgent.name AS refuelAgentName, refuelAgent.short_name AS refuelAgentShortName, refuelAirport.name AS refuelAirportName, refuelAirport.short_name AS refuelAirportShortName, refuelAirport.code_icao AS refuelAirportICAO, refuelAirport.code_iata AS refuelAirportIATA, refuelUnit.name AS refuelUnitName, flightAgent.name AS flightAgentName, flightAgent.short_name AS flightAgentShortName, flightAirOperator.name AS flightAirOperatorName, flightAirOperator.short_name AS flightAirOperatorShortName, flightAirOperator.code_icao AS flightAirOperatorICAO, flightAirOperator.code_iata AS flightAirOperatorIATA, flightAircraft.aircraft_type AS flightAircraftTypeId, flightAircraft.reg_number AS flightAircraftName, flightAircraftType.name AS flightAircraftTypeName, flightAlternativeAircraft1.aircraft_type AS flightAlternativeAircraftTypeId1, flightAlternativeAircraft1.reg_number AS flightAlternativeAircraftName1, flightAlternativeTypeAircraft1.name AS flightAlternativeAircraftTypeName1, flightAlternativeAircraft2.aircraft_type AS flightAlternativeAircraftTypeId2, flightAlternativeAircraft2.reg_number AS flightAlternativeAircraftName2, flightAlternativeTypeAircraft2.name AS flightAlternativeAircraftTypeName2 FROM flightRefuelForm LEFT JOIN flightBaseHeaderForm AS flight ON flightRefuelForm.headerId = flight.id LEFT JOIN library_kontragent AS refuelAgent ON refuelAgent.id = flightRefuelForm.agentId LEFT JOIN library_airport AS refuelAirport ON refuelAirport.id = flightRefuelForm.airportId LEFT JOIN library_unit AS refuelUnit ON refuelUnit.id = flightRefuelForm.unitId LEFT JOIN library_kontragent AS flightAgent ON flightAgent.id = flight.kontragent LEFT JOIN library_air_operator AS flightAirOperator ON flightAirOperator.id = flight.airOperator LEFT JOIN library_aircraft AS flightAircraft ON flightAircraft.id = flight.aircraftId LEFT JOIN library_aircraft_type AS flightAircraftType ON flightAircraftType.id = flightAircraft.aircraft_type LEFT JOIN library_aircraft AS flightAlternativeAircraft1 ON flightAlternativeAircraft1.id = flight.alternativeAircraftId1 LEFT JOIN library_aircraft_type AS flightAlternativeTypeAircraft1 ON flightAlternativeTypeAircraft1.id = flightAlternativeAircraft1.aircraft_type LEFT JOIN library_aircraft AS flightAlternativeAircraft2 ON flightAlternativeAircraft2.id = flight.alternativeAircraftId2 LEFT JOIN library_aircraft_type AS flightAlternativeTypeAircraft2 ON flightAlternativeTypeAircraft2.id = flightAlternativeAircraft2.aircraft_type WHERE flight.dateOrder BETWEEN '1357027481' AND '1388477081' AND flight.kontragent = '4' ORDER BY date DESC