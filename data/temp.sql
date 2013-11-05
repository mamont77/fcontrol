SELECT
flightPermissionForm.id AS id, 
flightPermissionForm.headerId AS headerId,
flightPermissionForm.agentId AS agentId,
flightPermissionForm.legId AS legId,
flightPermissionForm.countryId AS countryId,
flightPermissionForm.typeOfPermission AS typeOfPermission,
flightPermissionForm.permission AS permission,
agent.name AS agentName,
agent.address AS agentAddress,
agent.mail AS agentMail,
leg.apDepAirportId AS airportDepartureId,
leg.apArrAirportId AS airportArrivalId,
airportDeparture.code_icao AS airportDepartureICAO,
airportDeparture.code_iata AS airportDepartureIATA,
airportArrival.code_icao AS airportArrivalICAO,
airportArrival.code_iata AS airportArrivalIATA,
country.name AS countryName,
country.code AS countryCode
FROM flightPermissionForm
LEFT JOIN library_kontragent AS agent ON flightPermissionForm.agentId = agent.id
LEFT JOIN flightLegForm AS leg ON flightPermissionForm.legId = leg.id
LEFT JOIN library_airport AS airportDeparture ON leg.apDepAirportId = airportDeparture.id
LEFT JOIN library_airport AS airportArrival ON leg.apArrAirportId = airportArrival.id
LEFT JOIN library_country AS country ON flightPermissionForm.countryId = country.id
WHERE flightPermissionForm.headerId = '30'
ORDER BY flightPermissionForm.legId ASC, flightPermissionForm.id ASC